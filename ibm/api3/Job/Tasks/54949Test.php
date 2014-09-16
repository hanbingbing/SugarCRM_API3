<?php

class Job_Tasks_54949Test extends App_Job_Abstract
{
    private $user;
    private $task;
    private $error_id = 'test_54949_user_11223344';
    private $error_cnum = '1001011223344';

    private $departmant = 'test_54949_department';
    private $description = 'test 54949 user description';
    private $mobile = '549491234567';

    public function setup($params)
    {
        $this->user = $this->createUser(
            'test_54949_user_1',
            array('employee_cnum' => '10010')
        );
        $this->task = $this->createTasks('test_24949_task1');
        parent::setup($params);
    }

    public function run($params)
    {
        $this->apiEnableDumpResponse();
//        $this->cleanMode = App_Job_Abstract::CLEAN_DEFERRED;
        parent::run($params);


        //Test create success.
        $request = new Api_Request_RelateRecordByCustomIdApi_createRelatedLinkByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected('assertEquals', 'record.id', $this->task->id);
        $request->getParser()->setExpected('assertEquals', 'related_record.id', $this->user->id);

        $this->addRequest($request);



        //Test fetch failure: task id error;
        $request = new Api_Request_RelateRecordByCustomIdApi_fetchRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->error_id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected(
            'assertEquals',
            'error_message',
            "Could not find record: {$this->error_id} in module: Tasks"
        );

        $this->addRequest($request);


        //Test fetch failure: employee_cnum error;
        $request = new Api_Request_RelateRecordByCustomIdApi_fetchRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->error_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected('assertEquals', 'error_message', 'Could not find the related bean');

        $this->addRequest($request);


        //Test fetch success.
        $request = new Api_Request_RelateRecordByCustomIdApi_fetchRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected('assertEquals', 'id', $this->user->id);
        $request->getParser()->setExpected('assertEquals', '_module', 'Users');

        $this->addRequest($request);


        //Test update failure: task id error;
        $request = new Api_Request_RelateRecordByCustomIdApi_updateRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->error_id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected(
            'assertEquals',
            'error_message',
            "Could not find record: {$this->error_id} in module: Tasks"
        );

        $this->addRequest($request);

        //Test update failure: employee_cnum error;
        $request = new Api_Request_RelateRecordByCustomIdApi_updateRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->error_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected('assertEquals', 'error_message', 'Could not find the related bean');

        $this->addRequest($request);

        //Test update success.
        $request = new Api_Request_RelateRecordByCustomIdApi_updateRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id
        $request->setPayloads(
            array(
                "department" => $this->departmant,
                "description" => $this->description,
                "phone_home" => $this->mobile,
                "phone_mobile" => $this->mobile,
                "phone_work" => $this->mobile,
            )
        );

        $paraser = $request->getParser();

        $paraser->setExpectedDefaults();
        $paraser->setExpected('assertEquals', 'record.id', $this->task->id);
        $paraser->setExpected('assertEquals', 'related_record.id', $this->user->id);
        $paraser->setExpected('assertEquals', 'related_record.department', $this->departmant);
        $paraser->setExpected('assertEquals', 'related_record.description', $this->description);
        $paraser->setExpected('assertEquals', 'related_record.phone_home', $this->mobile);
        $paraser->setExpected('assertEquals', 'related_record.phone_mobile', $this->mobile);
        $paraser->setExpected('assertEquals', 'related_record.phone_work', $this->mobile);

        $this->addRequest($request);


        //Test delete failure: task id error;
        $request = new Api_Request_RelateRecordByCustomIdApi_deleteRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->error_id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected(
            'assertEquals',
            'error_message',
            "Could not find record: {$this->error_id} in module: Tasks"
        );

        $this->addRequest($request);

        //Test delete failure: employee_cnum error;
        $request = new Api_Request_RelateRecordByCustomIdApi_deleteRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->error_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected('assertEquals', 'error_message', 'Could not find the related bean');

        $this->addRequest($request);

        //Test delete success.
        $request = new Api_Request_RelateRecordByCustomIdApi_deleteRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected('assertEquals', 'record.id', $this->task->id);
        $request->getParser()->setExpected('assertEquals', 'related_record.id', $this->user->id);

        $this->addRequest($request);


        //Test fetch failure after delete.
        $request = new Api_Request_RelateRecordByCustomIdApi_fetchRelatedRecordByCustomId();
        $request->addResource('Tasks'); //set module name
        $request->addResource($this->task->id); //set record
        $request->addResource('additional_assignees_link'); //set link name
        $request->addResource('employee_cnum'); //set id_field
        $request->addResource($this->user->employee_cnum); //set remote id

        $request->getParser()->setExpected('assertEquals', 'error', 'not_found');
        $request->getParser()->setExpected('assertEquals', 'error_message', 'Could not find the related bean');

        $this->addRequest($request);

        $this->apiCall();
    }
}