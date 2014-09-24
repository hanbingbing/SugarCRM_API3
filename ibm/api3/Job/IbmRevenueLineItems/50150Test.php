<?php

class Job_IbmRevenueLineItems_50150Test extends App_Job_Abstract
{
    private $oppId = '9T-240Q48Q';
    public function setup($params){
        parent::setup($params);
    }

    public function cleanup($params){
        parent::cleanup($params);
    }

    public function run($params)
    {
        $this->setLoginUser('fh-level10user10','fh-level10user10');
        $this->apiEnableDumpResponse();
        $this->cleanMode = App_Job_Abstract::CLEAN_MANUAL;
//        $this->cleanMode =  App_Job_Abstract::CLEAN_DEFERRED;
        parent::run($params);

        $request = new Api_Request_RelateRecordApi_createRelatedRecord();
        $request->addResource('Opportunities'); //set module name
        $request->addResource($this->oppId); //set record id
        $request->addResource('opportun_revenuelineitems'); //set link name

        $request->getParser()->setExpectedDefaults();
        $request->setPayloadsJSON('{
            "doc_owner": "",
            "user_favorites": "",
            "search_type": "product",
            "revenue_type": "Signings",
            "duration": 12,
            "probability": "10",
            "p2c": 0,
            "green_blue_revenue": "Green",
            "assigned_bp_id": "",
            "alliance_partners": "",
            "igf_odds": "0",
            "igf_term": 1,
            "currency_id": "-99",
            "roadmap_status": "NIR",
            "assigned_user_id": "fh-level10user10",
            "assigned_user_name": "level10 (level10) user10",
            "following": "",
            "name": "",
            "description": "",
            "level10": "B7000",
            "level10_name": "Software",
            "level10_offering_type": "",
            "level15": "DM",
            "level15_name": "Information Management",
            "level20": "BKW00",
            "level20_name": "Software: Information Management InfoSphere Guardium",
            "level20_offering_type": "SVCS",
            "level30": "",
            "level30_name": "",
            "level30_offering_type": "",
            "level40": "",
            "level40_name": "",
            "level40_offering_type": "",
            "offering_template": "",
            "composite": "",
            "solution_id": "",
            "solution_name": "",
            "solution_code": "",
            "rli_browseSolution_template": "",
            "rli_cusid": "",
            "rli_id": "",
            "competitor": "",
            "roadmap_status_values": "",
            "revenue_amount": "15000",
            "currency_name": "",
            "ra_currency_id": "",
            "ra_currency_name": "",
            "ra_currency_symbol": "",
            "fra_currency_id": "",
            "fra_currency_name": "",
            "fra_currency_symbol": "",
            "bookable_value": "",
            "fcast_date_sign": "2015-01-18",
            "fcast_date_tran": "",
            "financed_rev_amount": "",
            "level_search": "",
            "account_id": "94b44ea5-f0ec-ee8c-80f0-53a165415dd3",
            "dep_level10": "",
            "dep_level15": "",
            "dep_stage": "",
            "participation_number": "",
            "opportuf4b1ties_ida": "",
            "product_information": "",
            "srv_work_type": "",
            "srv_billing_type": "",
            "srv_inqtr": "",
            "srv_inqtr_total": "0.000000",
            "srv_inqtr_status": "",
            "stg_fulfill_type": "",
            "stg_signings_type": "",
            "swg_contract": "",
            "swg_contract_values": "",
            "swg_book_new": "",
            "swg_book_rnwl": "",
            "swg_book_serv": "",
            "swg_tran_det": "",
            "swg_sign_det": "",
            "swg_annual_value": "0.000000",
            "igf_close_date": "",
            "igf_comments": "",
            "igf_competitor": "",
            "igf_financed_amount": "",
            "igf_owner_id": "",
            "igf_owner_name": "",
            "igf_roadmap_status": "",
            "igf_sales_stage": "",
            "igf_total_contract_value": "",
            "igf_volumes_recording_date": "",
            "ext_ref_id1_c": "",
            "ext_ref_id2_c": "",
            "last_updating_system_c": "",
            "last_updating_system_date_c": "",
            "relate_beans": ""
        }');

        $this->addRequest($request);

        $request = new Api_Request_RelateRecordApi_createRelatedRecord();
        $request->addResource('Opportunities'); //set module name
        $request->addResource($this->oppId); //set record id
        $request->addResource('opportun_revenuelineitems'); //set link name

        $request->getParser()->setExpectedDefaults();
        $request->setPayloadsJSON('{
            "doc_owner": "",
            "user_favorites": "",
            "search_type": "product",
            "revenue_type": "Signings",
            "duration": 12,
            "probability": "10",
            "p2c": 0,
            "green_blue_revenue": "Green",
            "assigned_bp_id": "",
            "alliance_partners": "",
            "igf_odds": "0",
            "igf_term": 1,
            "currency_id": "-99",
            "roadmap_status": "STR",
            "assigned_user_id": "fh-level10user10",
            "assigned_user_name": "level10 (level10) user10",
            "following": "",
            "name": "",
            "description": "",
            "level10": "B7000",
            "level10_name": "Software",
            "level10_offering_type": "",
            "level15": "DM",
            "level15_name": "Information Management",
            "level20": "BKW00",
            "level20_name": "Software: Information Management InfoSphere Guardium",
            "level20_offering_type": "SVCS",
            "level30": "",
            "level30_name": "",
            "level30_offering_type": "",
            "level40": "",
            "level40_name": "",
            "level40_offering_type": "",
            "offering_template": "",
            "composite": "",
            "solution_id": "",
            "solution_name": "",
            "solution_code": "",
            "rli_browseSolution_template": "",
            "rli_cusid": "",
            "rli_id": "",
            "competitor": "",
            "roadmap_status_values": "",
            "revenue_amount": "15000",
            "currency_name": "",
            "ra_currency_id": "",
            "ra_currency_name": "",
            "ra_currency_symbol": "",
            "fra_currency_id": "",
            "fra_currency_name": "",
            "fra_currency_symbol": "",
            "bookable_value": "",
            "fcast_date_sign": "2015-01-18",
            "fcast_date_tran": "",
            "financed_rev_amount": "",
            "level_search": "",
            "account_id": "94b44ea5-f0ec-ee8c-80f0-53a165415dd3",
            "dep_level10": "",
            "dep_level15": "",
            "dep_stage": "",
            "participation_number": "",
            "opportuf4b1ties_ida": "",
            "product_information": "",
            "srv_work_type": "",
            "srv_billing_type": "",
            "srv_inqtr": "",
            "srv_inqtr_total": "0.000000",
            "srv_inqtr_status": "",
            "stg_fulfill_type": "",
            "stg_signings_type": "",
            "swg_contract": "",
            "swg_contract_values": "",
            "swg_book_new": "",
            "swg_book_rnwl": "",
            "swg_book_serv": "",
            "swg_tran_det": "",
            "swg_sign_det": "",
            "swg_annual_value": "0.000000",
            "igf_close_date": "",
            "igf_comments": "",
            "igf_competitor": "",
            "igf_financed_amount": "",
            "igf_owner_id": "",
            "igf_owner_name": "",
            "igf_roadmap_status": "",
            "igf_sales_stage": "",
            "igf_total_contract_value": "",
            "igf_volumes_recording_date": "",
            "ext_ref_id1_c": "",
            "ext_ref_id2_c": "",
            "last_updating_system_c": "",
            "last_updating_system_date_c": "",
            "relate_beans": ""
        }');
        $this->addRequest($request);

        $this->apiCall();
    }
}