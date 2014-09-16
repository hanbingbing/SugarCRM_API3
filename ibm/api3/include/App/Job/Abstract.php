<?php

include_once "include/App/Job/Interface.php";
include_once "include/Api/Factory.php";

include_once "include/Db/Factory.php";

/**
 * app job abstract
 */
abstract class App_Job_Abstract extends  App_Job_CreateBeans implements App_Job_Interface
{
    // Jobname
    public $job;

    // registry
    protected $reg;

    // debug mode
    protected $debug = false;

    // count of assertions passed
    protected $passCount = 0;

    // count of assertions failed
    protected $failCount = 0;

    // list of error message
    protected $failMessages = array();

    // accumulated pass/fail assertion results
    protected $resultsStatusString = '';

    // list of sql commands to run when done
    protected $cleanupSQL = array();


    // list of parsers across requests
    protected $allParsers = array();

    protected $inTestSuite = false;

    // Job execution cycle
    protected $jobCycles = array(
        'preSetup' => false,
        'setup' => false,
        'preRun' => false,
        'run' => true, // run is required
        'postRun' => false,
        'test' => false,
        'cleanup' => true
    );

    const CLEAN_AUTOMATIC = 'automatic';
    const CLEAN_DEFERRED = 'deferred';
    const CLEAN_MANUAL = 'manual';

    // how to cleanup
    public $cleanMode = App_Job_Abstract::CLEAN_AUTOMATIC;  // deferred, manual

    public $api;

    /**
     *
     * Constructor
     */
    public function __construct()
    {
        $this->job = get_class($this);
        $this->reg = App_Registry::getInstance();
    }

    public function __destruct()
    {
        $this->cleanup();
    }

    /*
     * track each request made for later parsing
     */
    public function addRequest($request)
    {
        $this->requests[] = $request;
    }

    /*
     * track each request made for later parsing
     */
    public function restRequests()
    {
        unset($this->requests);
        $this->requests = array();
    }

    /**
     *
     * Execute job
     * @param unknown_type $params optional
     */
    public function exec($params = array())
    {
        if (isset($params['inTestSuite'])) {
            $this->inTestSuite = $params['inTestSuite'];
            unset($params['inTestSuite']);
        }

        // execute job cycles
        foreach ($this->jobCycles as $cycle => $required) {
            if (method_exists($this, $cycle)) {
                $this->$cycle($params);
            } else {
                if ($required) {
                    die("Missing '$cycle' in job execution");
                }
            }
        }

        // set xdebug flag to original state
        $this->api->resetXdebug();
    }

    /**
     *
     * @see App_Job_Interface::preSetup()
     */
    public function preSetup($params = array())
    {
        // for deferred cleanup, this is the time to run it
        if ($this->cleanMode == App_Job_Abstract::CLEAN_AUTOMATIC || $this->cleanMode == App_Job_Abstract::CLEAN_DEFERRED) {
            $file = "." . $this->job . ".cln";
            if (file_exists($file)) {
                $cleanupSQL = unserialize(file_get_contents($file));
                unlink($file);
                if (isset($GLOBALS['db'])) {
                    $db = $GLOBALS['db'];
                } else {
                    $db = Db_Factory::getInstance();
                }
                foreach ($cleanupSQL as $sql) {
                    if ($this->debug) {
                        echo "CLEANUP SQL: $sql\n";
                    }
                    $db->query($sql);
                }
                $db->commit();
            }
        }
    }
    
    /**
     *
     * @see App_Job_Interface::setup()
     */
    public function setup($params = array())
    {
        // we need any bean saves during setup to be committed to db before
        // a web service request tries to access them
        $GLOBALS['db']->commit();
    }

    /**
     *
     * @see App_Job_Interface::preRun()
     */
    public function preRun($params = array())
    {
        // setup api object
        $this->api = Api_Api::getInstance();

        $this->api->reset();

        $this->restRequests();
        // setup db object
//        $this->db =& Db_Factory::getInstance();
    }

    /**
     *
     * @see App_Job_Interface::run()
     */
    public function run($params = array())
    {
        // we dont login into the api at preRun so we are able to override
        // api options at preRun where applicable
        $this->api->login();
    }

    /**
     * @see App_Job_Interface::test()
     *
     */
    public function test($params = array())
    {
        $first = true;
        while($response = $this->api->shiftResponse()){
            if($response->request instanceof Api_Request_OAuth2Api_token){
                continue;
            }

            $parser = $response->request->getParser();

            $parser->setJobName(get_class($this));

            if ($parser) {
                $this->allParsers[] = $parser;
                if ($parser->debug) {
                    $parser->resultsStatusString .= "R";
                } else {
                    if($first){
                        echo get_class($this)."=>R";
                        $first = false;
                    }
                }
                $results = $parser->parseResponse($response);
            } else {
                if ($this->debug) {
                    echo "No parser for " . $request->getModelName() . "\n";
                }
            }
        }


        // accumulate results
        foreach ($this->allParsers as $parser) {
            $this->passCount += $parser->passCount;
            $this->failCount += $parser->failCount;
            $this->resultsStatusString .= $parser->resultsStatusString;
            if (isset($parser->failMessages) && count($parser->failMessages)) {
                foreach ($parser->failMessages as $msg) {
                    $this->failMessages[] = $msg;
                }
            }
            if (isset($parser->cleanupSQL) && count($parser->cleanupSQL)) {
                foreach ($parser->cleanupSQL as $sql) {
                    $this->cleanupSQL[] = $sql;
                }
            }
        }

        if (!empty($this->resultsStatusString)) {
            echo $this->resultsStatusString;
        }
        echo "\n"; // finish results status
        if (!$this->inTestSuite) {
            echo "Tests passed: " . $this->passCount . "\n";
            echo "Tests failed: " . $this->failCount . "\n";
            if ($this->failCount && count($this->failMessages)) {
                echo "Fail messages: \n\t" . implode("\n\t", $this->failMessages) . "\n";
            }
            $c = get_class($this);
            $s = $this->failCount == 0 ? "PASS" : "FAIL";
            echo "Job status for $c: $s\n";
        }
    }

    /**
     *
     * @see App_Job_Interface::postRun()
     */
    public function postRun($params = array())
    {
    }

    /**
     *
     * @see App_Job_Interface::cleanup()
     */
    public function cleanup($params = array())
    {

        $db = '';
        if (isset($GLOBALS['db'])) {
            $db = $GLOBALS['db'];
        } else {
            $GLOBALS['db'] = $db = DBManagerFactory::getInstance();
        }

        if (isset($this->cleanupReleationships) && !empty($this->cleanupReleationships)) {
            foreach ($this->cleanupReleationships as $relEment) {
                $relEment['lhs']->load_relationship($relEment['rel']);
                $relEment['lhs']->$relEment['rel']->delete($relEment['rhs']->id);
            }
        }

        unset($this->cleanupReleationships);

        if ($this->cleanMode == App_Job_Abstract::CLEAN_AUTOMATIC) {
            $db = '';
            if (isset($GLOBALS['db'])) {
                $db = $GLOBALS['db'];
            } else {
                $GLOBALS['db'] = $db = DBManagerFactory::getInstance();
            }
            if (isset($this->cleanupSQL)) {
                foreach ($this->cleanupSQL as $sql) {
                    if ($this->debug) {
                        echo "CLEANUP SQL: $sql\n";
                    }
                    $db->query($sql);
                }
            }
    
            // we need to commit these changes in case of a suite run so
            // they're good to go for the next test
            if (isset($GLOBALS) && isset($GLOBALS['db']) && is_object($GLOBALS['db'])) {
                $GLOBALS['db']->commit();
            }
        } elseif ($this->cleanMode == App_Job_Abstract::CLEAN_DEFERRED && count($this->cleanupSQL)) {
            // deferred mode for developers wanting to look at db
            // after a test has run. Save cleanup steps, but run them
            // on next job invocation
            $file = "." . $this->job . ".cln";
            file_put_contents($file, serialize($this->cleanupSQL));
        }
        unset($GLOBALS['apiRegisteredParsers']);
    }

    /**
     *
     * Wrapper so save last api request
     */
    public function saveApiRequest()
    {
        $this->api->saveLastRequest($this->job);
    }

    /**
     *
     * Wrapper so save last api response
     */
    public function saveApiResponse()
    {
        $this->api->saveLastResponse($this->job);
    }

    /**
     *
     * Wrapper so save both last api request and response
     */
    public function saveApiCall()
    {
        $this->saveApiRequest($this->job);
        $this->saveApiResponse($this->job);
    }

    /**
     * @return int
     */
    public function getFailCount()
    {
        return $this->failCount;
    }

    /**
     * @return array
     */
    public function getFailMessages()
    {
        return $this->failMessages;
    }

    /**
     * @return int
     */
    public function getPassCount()
    {
        return $this->passCount;
    }

    public function apiCall()
    {
        if($this->inTestSuite){
            $this->api->disableDumpResponse();
        }
        foreach($this->requests as $request){
            $this->api->sugarCall($request);
        }

    }

    /**
     * set username and password for login user.
     * @param $username
     * @param $password
     */
    public function setLoginUser($username,$password){
        $this->api->sugarUser = $username;
        $this->api->sugarPassword = $password;
    }

    public function apiEnableDumpResponse(){
        $this->api->enableDumpResponse();
    }

    public function apiDisableDumpResponse(){
        $this->api->disableDumpResponse();
    }

    public function getLastCreatedRecord(){
        unset($GLOBALS['last_created_record']);
        return Api_Response_Parser_Abstract::USE_LAST_CREATED_RECORD;
    }
}
