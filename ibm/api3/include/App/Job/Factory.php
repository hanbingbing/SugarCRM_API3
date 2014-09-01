<?php

include_once "include/Utils.php";

/**
 * job factory
 */
class App_Job_Factory
{
    // job object cache
    protected static $jobCache = array();

    /**
     *
     * Singleton
     */
    private function __construct()
    {
    }

    /**
     *
     * Job loader
     */
    public static function load($job)
    {
        if (!isset(self::$jobCache[$job])) {
            if (str_begin($job, 'Job_')) {
                $className = $job;
            } else {
                $className = "Job_{$job}";
            }

            $file = str_replace("_", "/", $className);
            $incFile = Utils::apiFileExists($file . ".php");
            if ($incFile) {
                include_once $incFile;
                self::$jobCache[$job] = new $className();
            } else {
                echo "No Job file found: $file\n";
                return;
            }
        }
        return self::$jobCache[$job];
    }
}
