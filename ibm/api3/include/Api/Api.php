<?php
include_once "include/Api/Interface.php";
include_once "include/Utils.php";

/**
 * api abstract
 */
class Api_Api implements Api_Interface
{
    // SugarCRM settings
    public $sugarUser;
    public $sugarPassword;
    public $sugarGrantType;
    public $sugarClientId;
    public $sugarEncryption;
    public $sugarEncryptionKey;
    public $sugarApiApp;

    //API access token
    protected $accessToken;
    //API download token;
    protected $downloadToken;
    //API refresh token
    protected $refreshToken;
    //API token Type
    protected $tokenType;

    // xdebug support
    protected $xdebug = false;
    protected $xdebugIdeKey = '1';
    protected $xdebugOnLogin = false;

    // Registry object
    protected $reg;

    // Var_dump Response
    public $dumpResponse = false;


    // last Response object
    protected $lastResponse;
    protected $responses = array();

    // implicit logout
    public $autoLogout = false;


    protected static $instance;


    /**
     *
     * API object loader
     */
    public static function &getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Api_Api();
        }
        return self::$instance;
    }

    /**
     *
     * Constructor
     */
    public function __construct()
    {
        // ref registry
        $this->reg =& App_Registry::getInstance();

        // load config from registry
        $this->sugarUser = $this->reg->get('sugar_user');
        $this->sugarPassword = $this->reg->get('sugar_password');
        $this->sugarGrantType = $this->reg->get('grant_type');
        $this->sugarClientId = $this->reg->get('client_id');
        $this->sugarEncryption = $this->reg->get('sugar_encryption');
        $this->sugarEncryptionKey = $this->reg->get('sugar_encryption_key');


        // xdebug init
        $this->resetXdebug();
    }

    public function reset()
    {
        $this->responses = array();
    }

    /**
     *
     * Destructor
     */
    public function __destruct()
    {
        if ($this->autoLogout) {
            $this->logout();
        }
    }

    /**
     *
     * Login into SugarCRM and get a session
     */
    public function login()
    {
        if (!empty($this->accessToken)) {
            return;
        }

//        // password
//        if ($this->sugarEncryption == 'PLAIN') {
//            $password = $this->sugarPassword;
//            $encryption = 'PLAIN';
//        } elseif ($this->sugarEncryption == '3DES') {
//            $password = $this->returnEncryptedPassword();
//            $encryption = '3DES';
//        } else {
//            $password = md5($this->sugarPassword);
//            $encryption = '';
//        }
//
//        // login arguments
//        $args = array(
//            'user_auth' => array(
//                'user_name' => $this->sugarUser,
//                'password' => $password,
//            ),
//            $this->sugarApiApp,
//        );
//
//        // add encryption if specified
//        if ($encryption) {
//            $args['user_auth']['encryption'] = $encryption;
//        }

        $rquest = new Api_Request_OAuth2Api_token();
        $rquest->setPayloads(
            array(
                'grant_type' => $this->sugarGrantType,
                'username' => $this->sugarUser,
                'password' => $this->sugarPassword,
                'client_id' => $this->sugarClientId,
                'client_secret' => ''
            )
        );

        $this->sugarCall($rquest);
        if (empty($this->lastResponse->access_token)) {
            print_r($this->lastResponse);
            die('SugarCRM: unable to login');
        }

        $this->accessToken = $this->lastResponse->access_token;
        $this->tokenType = $this->lastResponse->token_type;
        $this->downloadToken = $this->lastResponse->download_token;
        $this->refreshToken = $this->lastResponse->refresh_token;

    }

    /**
     *
     * LDAP symmetric password encryption
     * @param string $password
     */
    protected function returnEncryptedPassword()
    {
        // encryption -> see service/core/SoapHelperWebService.php
        $key = substr(md5($this->sugarEncryptionKey), 0, 24);
        $iv = "password";
        $encr_pwd = unpack("H*", mcrypt_cbc(MCRYPT_3DES, $key, $this->sugarPassword, MCRYPT_ENCRYPT, $iv));
        return $encr_pwd[1];
    }


    /**
     *
     * Logout to cleanup session
     */
    public function logout()
    {
        if (!empty($this->sugarSession)) {
            $args = array(
                'session' => $this->sugarSession,
            );
            $this->sugarCall('logout', $args);
        }
    }

    public function sugarCall($request)
    {
        // add session to call
        if (!($request instanceof Api_Request_OAuth2Api_token) && empty($this->accessToken)) {
                $this->login();
        }
        $this->lastResponse = $this->call($request);
        $this->responses[] = $this->lastResponse;

        // dump Response
        if ($this->dumpResponse && !($request instanceof Api_Request_OAuth2Api_token)) {
            print_r($this->lastResponse);
        }

        return $this->lastResponse;
    }

    public function getXdebugUrl($url)
    {
        if ($this->xdebug) {
            $params = sprintf("XDEBUG_SESSION_START=%s", $this->xdebugIdeKey);
            $url .= ((strpos($url, '?') === false) ? '?' : '&') . $params;

            return $url;
        }

        return $url;
    }

    public function enableXdebug()
    {
        $this->xdebug = true;
    }

    public function disableXdebug()
    {
        $this->xdebug = false;
    }

    public function resetXdebug()
    {
        $this->xdebug = $this->reg->get('xdebug');
        $this->xdebugIdeKey = $this->reg->get('xdebug_idekey');
        $this->xdebugOnLogin = false;
    }

    public function setXdebugOnLogin($xdebugOnLogin)
    {
        $this->xdebugOnLogin = $xdebugOnLogin;
    }

    public function getXdebugOnLogin()
    {
        return $this->xdebugOnLogin;
    }


    /**
     *
     * Save request to temp directory
     */
    public function saveLastRequest($file)
    {
        $prefix = $this->reg->diffDir;
        Utils::dumpToFile("$prefix/temp/$file-request.xml", $this->getLastRequest());
    }

    /**
     *
     * Save response to temp directory
     */
    public function saveLastResponse($file)
    {
        $prefix = $this->reg->diffDir;
        Utils::dumpToFile("$prefix/temp/$file-response.xml", $this->getLastResponse());
    }

    /**
     *
     * Return Response object
     */
    public function getResponseByIndex($index)
    {
        return $this->responses[$index];
    }

    /**
     *
     * Return first Response object
     */
    public function getFirstResponse()
    {
        return $this->getResponseByIndex[0];
    }

    /**
     *
     * Return last Response object
     */
    public function getLastResponse()
    {
        return $this->getResponseByIndex(count($this->responses) - 1);
    }

    public function shiftResponse()
    {
        return array_shift($this->responses);
    }

    public function popResponse()
    {
        return array_pop($this->responses);
    }

    protected function call($request)
    {
        $type = $request->getRequestType();
        $params = array();

        $payloads = $request->getPayloads();

        if (!($request instanceof Api_Request_OAuth2Api_token)) {
            $params = array_merge(
                $params,
                array('headers' => array('OAuth-Token' => $this->accessToken))
            );
        } elseif (($request instanceof Api_Request_OAuth2Api_token)) {
            if($this->xdebug && $this->xdebugOnLogin){
                $payloads = array_merge(
                    $payloads,
                    array('XDEBUG_SESSION_START' => $this->xdebugIdeKey)
                );
            }
        }elseif($this->xdebug){
            $payloads = array_merge(
                $payloads,
                array('XDEBUG_SESSION_START' => $this->xdebugIdeKey)
            );
        }

        $rc = new Api_Rest_RestClient($params);
        $Response = $rc->$type($request->getRESTfulURI(), $payloads);
        $this->lastResponse = $Response->decode_response();

        $this->lastResponse->request = $request;
        return $this->lastResponse;

    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }


    public function exec($params = array())
    {
        if (isset($params['inTestSuite'])) {
            $this->inTestSuite = true;
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
        $this->resetXdebug();
    }

    public function enableDumpResponse()
    {
        $this->dumpResponse = true;
    }


    public function disableDumpResponse()
    {
        return $this->dumpResponse = false;
    }


}
