<?php

/**
 * Job methods to step though
 */
interface App_Job_Interface
{
    public function setup($params);

    public function preRun($params);

    public function run($params);

    public function postRun($params);

    public function test($params);

    public function cleanup($params);
}


