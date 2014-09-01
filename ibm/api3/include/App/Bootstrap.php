<?php
include_once "include/App/Registry.php";
include_once "include/App/Job/Factory.php";

/**
 * app bootstrap
 */
class App_Bootstrap
{
    // registry
    public $registry;

    // difference between sugar root and api root
    public $diffDir;

    // job object
    protected $job;

    protected $passCount = 0;

    protected $failCount = 0;

    protected $failMessages = array();

    /**
     *
     * Constructor
     */
    public function __construct($diffDir = 'ibm/api3')
    {
        $this->diffDir = $diffDir;
        // autoloader
        $this->registerAutoloader();

        // registry
        $this->registry =& App_Registry::getInstance($diffDir);

        // runtime config settings
        foreach ($this->registry->get('php_ini') as $dir => $value) {
            ini_set($dir, $value);
        }
    }

    /**
     *
     * Register auto loader method
     */
    private function registerAutoloader()
    {
        spl_autoload_register(array($this, 'autoLoad'), true, false);
    }

    /**
     *
     * Perform auto class loading
     * @param unknown_type $className
     */
    private function autoLoad($className)
    {
        $class = explode('_', $className);
        $baseDir = 'include';
        $classFile = $this->diffDir . '/' . $baseDir . '/' . implode('/', $class) . '.php';

        if (!is_file($classFile)) {
            // if can't find the file in include,find it in Job
            $baseDir = 'Job';
            $classFile = $this->diffDir . '/' . $baseDir . '/' . implode('/', $class) . '.php';

            // return if file not found, other loaders may be active so dont die here
            if (!is_file($classFile)) {
                return;
            }
        }

        require_once($classFile);

        // check if class available
        if (class_exists($className) || interface_exists($className)) {
            return;
        }

        die("Autoloader failure: $className not found in $classFile");
    }

    /**
     *
     * Execute our application
     * @param unknown_type $job
     */
    public function exec($job = null, $inTestSuite = false)
    {
        if (empty($job)) {
            die("no job name.\n");
        }

        // execute our job
        $this->job = App_Job_Factory::load($job);

        if (is_string($this->job)) {
            return;
        }
        $this->job->exec(array('inTestSuite' => $inTestSuite));

        // get test resutl
        $this->failCount += $this->job->getFailCount();
        $this->failMessages = array_merge($this->failMessages, $this->job->getFailMessages());
        $this->passCount += $this->job->getPassCount();

    }

    public function isPassed()
    {
        if ($this->failCount == 0 && count($this->failMessages) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getFailCount()
    {
        return $this->failCount;
    }

    /**
     * @return string
     */
    public function getFailMessages()
    {
        $result = "";
        foreach ($this->failMessages as $message) {
            $result .= $message . "\n";
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getPassCount()
    {
        return $this->passCount;
    }


    public function getTestResult()
    {
        $result = "Tests passed: " . $this->passCount . "\n";
        $result .= "Tests failed: " . $this->failCount . "\n";
        if ($this->failCount && count($this->failMessages)) {
            $result .= "Fail messages: \n\t" . implode("\n\t", $this->failMessages) . "\n";
        }

        return $result;
    }


}
