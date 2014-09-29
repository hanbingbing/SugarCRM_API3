<?php

class Job_test extends App_Job_Abstract
{
    public function run($params)
    {
        $this->apiEnableDumpResponse();
        parent::run($params);
        $request = new Api_Request_CurrentUserApi_retrieve();
        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected(
            'assertIndexOfArrayEquals',
            'current_user.my_teams',
            array(
                'field' => 'id',
                'value' => '1',
            )
        );
        $this->addRequest($request);
        $this->apiCall();
    }
}