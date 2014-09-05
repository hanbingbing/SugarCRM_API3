#!/usr/bin/php
<?php
global $argv;
error_reporting(E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR);
if (!defined('sugarEntry')) define('sugarEntry', true);

// go to where script is
$scriptDir = dirname($argv[0]);
if (!chdir($scriptDir)) {
    sugar_dir("Cannot cd to script directory");
}

// find sugar dir
$cwd = getcwd();
$apiDir = $cwd;
$sugarDir = findSugar($cwd);
$diffDir = str_replace("$sugarDir/", "", $apiDir);
if (empty($diffDir)) {
    $diffDir = '.';
}
$curCount = count(explode('/', $cwd));
$sugCount = count(explode('/', $sugarDir));
for ($i = 0; $i < $curCount - $sugCount; $i++) {
    if (empty($rel)) {
        $rel = "..";
    } else {
        $rel .= "/..";
    }
}
// find where we are
set_include_path($apiDir . PATH_SEPARATOR . get_include_path());

//change directories to where this file is located.
//this is to make sure it can find dce_config.php
chdir(realpath(__DIR__) . "/" . $rel);

/**
 * Load all nesessary config data and dependencies
 */
require_once('include/entryPoint.php');
require_once 'custom/include/Helpers/IBMHelper.php';
require_once('include/App/Bootstrap.php');

try {
    $app = new App_Bootstrap($diffDir);
    if ($argc != 2 && $argc != 3) {
        displayCommandErrorMessage();
    } elseif ($argc == 3) {
        $opts = getopt('j:');
        $job = $opts['j'];
        $app->exec($job);
    } elseif ($argc == 2) {
        $jobDir = $argv[1];
        $jobDir = str_replace(
            array('.' . DIRECTORY_SEPARATOR, '..' . DIRECTORY_SEPARATOR),
            array($apiDir . DIRECTORY_SEPARATOR, $apiDir . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR),
            $jobDir
        );
        $jobs = array();
        getFiles($jobs, $jobDir, "/.*\.php/");
        foreach ($jobs as $jobFilePath) {
            $job = str_replace(
                array(
                    $apiDir . DIRECTORY_SEPARATOR,
                    DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR,
                    DIRECTORY_SEPARATOR,
                    '.php'
                ),
                array('', '_', '_', ''),
                $jobFilePath
            );
            $app->exec($job, true);
        }
        echo $app->getTestResult();
    }
} catch (SugarCLIException $e) {
    sugar_die($e->getMessage());
}


sugar_cleanup(false);

function findSugar($path)
{
    if (empty($path) || $path == '/') {
        sugar_die("Sugar installation not found.");
    }
    $file = $path . "/config_override.php";
    if (file_exists($file)) {
        return $path;
    }
    $newPath = dirname($path);
    return findSugar($newPath);
}

function displayCommandErrorMessage()
{
    sugar_die("Usage:\n\trun.php -j job_name\nor\n\trun.php job_directory\n");
}
