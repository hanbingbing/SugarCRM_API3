<?php

class Job_Tasks_41709Test extends App_Job_Abstract
{
    private $user;
    private $search_cnum = '111222333' ;

    public function setup($params)
    {
        $this->user = $this->createUser(
            'test_41709_user_1',
            array('employee_cnum' => '10010')
        );
        parent::setup($params);
    }

    public function run($params)
    {
        $this->apiEnableDumpResponse();
//        $this->cleanMode = App_Job_Abstract::CLEAN_DEFERRED;
        parent::run($params);

        $request = new Api_Request_ModuleApi_createRecord();
        $request->addResource('Tasks'); //set module name
        $request->getParser()->setExpected('assertEquals', 'error', 'missing_parameter');
        $request->getParser()->setExpected(
            'assertEquals',
            'error_message',
            "Can't find assigned user by the employee_cnum: " . $this->search_cnum
        );
        $request->setPayloads(
            array(
                "employee_cnum" => $this->search_cnum,
                "created_by" => "fh-level10user10",
                "name" => "TestDefect41709",
                "description" => "CreateTaskTest Description",
                "status" => "In Progress",
                "date_due" => "2015-12-06T17:00:51.786Z",
                "priority" => "High",
                "call_type" => "Technical_Sales_Activity",
                "deleted" => 0,
                "team_id" => 1,
                "version" => 1
            )
        );
        $this->addRequest($request);


        $request = new Api_Request_ModuleApi_createRecord();
        $request->addResource('Tasks'); //set module name
        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected('assertEquals', 'assigned_user_id', $this->user->id);

        $request->setPayloads(
            array(
                "employee_cnum" => $this->user->employee_cnum,
                "created_by" => "fh-level10user10",
                "name" => "TestDefect41709",
                "description" => "CreateTaskTest Description",
                "status" => "In Progress",
                "date_due" => "2015-12-06T17:00:51.786Z",
                "priority" => "High",
                "call_type" => "Technical_Sales_Activity",
                "deleted" => 0,
                "team_id" => 1,
                "version" => 1
            )
        );
        $this->addRequest($request);


        $this->apiCall();
    }
}