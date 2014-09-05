<?php

class Job_IbmRoadmaps_createTest extends App_Job_Abstract
{
    private $acc;
    private $oppty = null;
    private $ibmProduct10 = null;
    private $ibmProduct15 = null;
    private $user = null;
    private $rliId = 'test_rli_46109';
    public function setup($params){
        $this->acc = $this->createAccount('test_acct46109');
        $this->user = $this->createUser('test_user_46109_1');
        $this->ibmProduct10 = $this->createIbmProduct(
            'test_pro_46109_10',
            array(
                'level' => '10'
            )
        );
        $this->ibmProduct15 = $this->createIbmProduct(
            'test_pro_46109_15',
            array(
                'level' => '15',
                'parent_id' => $this->ibmProduct10->id,
                'source_parent_id' => $this->ibmProduct10->id
            )
        );
        $this->oppty = $this->createOpportunity(
            'test_oppty_46109_1',
            array(
                'assigned_user_id' => $this->user->id,
                'account_id' => $this->acc->id
            )
        );
        $this->relationshipAdd("assigned_user_link", $this->oppty, $this->user);

        parent::setup($params);
    }

    public function cleanup($params){
        parent::cleanup($params);
    }

    public function run($params)
    {
        $this->apiEnableDumpResponse();
//        $this->cleanMode = App_Job_Abstract::CLEAN_MANUAL;
        parent::run($params);

        $request = new Api_Request_ModuleApi_createRecord();
        $request->addResource('ibm_RevenueLineItems'); //set module name
        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected('assertEquals', 'id', $this->rliId);
        $request->setPayloads(
            array(
//                'id' => $this->rliId,
                'level10' => $this->ibmProduct10->id,
                'level15' => $this->ibmProduct15->id,
                'assigned_user_id' => $this->user->id,
                'swg_book_new' => 'TRANS',
                'swg_tran_det' => 'ONE',
                'revenue_amount' => '3343434',
                'fcast_date_sign' => '05/20/2014',
                'fcast_date_tran' => '05/20/2014',
                'probability' => '10',
                'green_blue_revenue' => 'Green',
            )
        );
        $this->addRequest($request);

//        $request = new Api_Request_RelateApi_filterRelated();
//        $request->addResource('ibm_RevenueLineItems');
//        $request->addResource('68a9e90f-faf3-68e7-4a43-5406c08f25dc');
//        $request->addResource('assigned_user_link');
//
//        $request->getParser()->setExpectedDefaults();
//        $request->getParser()->setExpected(
//            'assertIndexOfArrayEquals',
//            'records',
//            array(
//                'field' => 'id',
//                'value' => $this->user->id,
//            )
//        );
//        $request->getParser()->setExpected(
//            'assertIndexOfArrayEquals',
//            'records',
//            array(
//                'field' => '_module',
//                'value' => 'Users',
//            )
//        );
//
//        $this->addRequest($request);


        $request = new Api_Request_RelateApi_filterRelated();
        $request->addResource('ibm_RevenueLineItems');
        $request->addResource('68a9e90f-faf3-68e7-4a43-5406c08f25dc');
//        $request->addResource($this->rliId);
        $request->addResource('ibm_roadmap_link');

        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected(
            'assertIndexOfArrayEquals',
            'records',
            array(
                'field' => '_module',
                'value' => 'ibm_Roadmaps',
            )
        );

        $this->addRequest($request);

        $this->apiCall();
    }
}