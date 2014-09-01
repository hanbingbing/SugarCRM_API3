<?php

/**
 * app registry
 */
class App_Registry
{
    // Singleton
    protected static $instance;

    // key/value pair registry
    protected $registry = array();

    // difference between sugar root and api root
    public $diffDir;
    
    /**
     *
     * Constructor
     */
    private function __construct($diffDir = 'ibm/api3')
    {
        $this->diffDir = $diffDir;
        $this->loadConfig();
    }

    /**
     *
     * Load configuration from config.php
     */
    protected function loadConfig()
    {
        include($this->diffDir . '/config.php');
        if (isset($config)) {
            foreach ($config as $key => $value) {
                $this->set($key, $value);
            }
        } else {
            sugar_die("Cannot find api config");
        }
    }

    /**
     *
     * Singleton instance loader
     */
    public static function &getInstance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    /**
     * 
     * Registry key/value pair setter
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public function set($key, $value)
    {
        $this->registry[$key] = $value;
    }

    /**
     * 
     * Registry key/value pair getter
     * @param unknown_type $key
     */
    public function get($key = null)
    {
        if ($key) {
            if (isset($this->registry[$key])) {
                return $this->registry[$key];
            }
            return null;
        } else {
            return $this->registry;
        }
    }
}
