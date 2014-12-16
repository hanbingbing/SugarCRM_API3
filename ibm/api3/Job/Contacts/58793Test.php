<?php

/**
 * Class Job_Contacts_58793Test
 */
class Job_Contacts_58793Test extends App_Job_Abstract
{
    private $call;
    private $contact;

    private $call_2;
    private $account;
    private $con;

    public function setup($params)
    {
        //contact not relate to account
        $this->contact = $this->createContact('test_58793_con');
        $this->call = $this->createCalls('test_58793_call');
        $this->relationshipAdd('contacts', $this->call, $this->contact);

        //contact relate to account
        $this->account = $this->createAccount('test_58793_acc');
        $this->con = $this->createContact('test_58793_contact');
        $this->relationshipAdd('accounts',$this->con,$this->account);

        $this->call_2 = $this->createCalls('test_58793_call_2');
        $this->relationshipAdd('contacts', $this->call_2, $this->con);

        parent::setup($params);
    }

    public function run($params)
    {
//        $this->apiEnableDumpResponse();
//        $this->cleanMode = App_Job_Abstract::CLEAN_DEFERRED;
        parent::run($params);


        //Test filter contact not relate to account.
        $request = new Api_Request_RelateApi_listRelatedRecords();
        $request->addResource('Calls'); //set module name
        $request->addResource($this->call->id); //set record
        $request->addResource('contacts'); //set link name

        $ext = new Api_Request_RequestExtension();
        $ext->setExtensionsString(
            'primary_first=1&fields=name%2Cfirst_name%2Clast_name%2Cphone_home%2Cphone_mobile%2Cphone_work%2C' .
            'phone_other%2Cphone_fax%2Cassistant%2Cassistant_phone%2Caccount_name%2Caccount_alt_lang_name_id%2C' .
            'account_alt_lang_name%2Cext_ref_id2_c%2Cext_ref_id1_c%2Caddress_alternate_ext_ref_c%2C' .
            'address_primary_ext_ref_c%2Clast_updating_system_c%2Caccount_name_denormal%2Cfull_name%2Ctitle%2C' .
            'preferred_name_c%2Ckey_contact_c%2Ccontact_status_c%2Cprimary_address_street%2Cprimary_address_city%2' .
            'Cprimary_address_state%2Cprimary_address_country%2Cprimary_address_postalcode%2Caddress_suppressed%2C' .
            'supp_perm_c%2Cemail%2Cphone_work_suppressed%2Cphone_mobile_suppressed%2Cphone_code%2Csalutation%2C' .
            'alt_lang_first_c%2Calt_lang_last_c%2Calt_lang_preferred_name_c%2Caccount_id%2Cemail1%2Cmy_favorite%2C' .
            'following%2Cassigned_user_id&max_num=5&order_by=date_modified%3Adesc'
        );
        $request->addResource($ext);

        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected(
            'assertIndexOfArrayEquals',
            'records',
            array(
                'field' => 'id',
                'value' => $this->contact->id
            )
        );

        $this->addRequest($request);

        //Test filter contact relate to account.
        $request = new Api_Request_RelateApi_listRelatedRecords();
        $request->addResource('Calls'); //set module name
        $request->addResource($this->call_2->id); //set record
        $request->addResource('contacts'); //set link name

        $ext = new Api_Request_RequestExtension();
        $ext->setExtensionsString(
            'primary_first=1&fields=name%2Cfirst_name%2Clast_name%2Cphone_home%2Cphone_mobile%2Cphone_work%2C' .
            'phone_other%2Cphone_fax%2Cassistant%2Cassistant_phone%2Caccount_name%2Caccount_alt_lang_name_id%2C' .
            'account_alt_lang_name%2Cext_ref_id2_c%2Cext_ref_id1_c%2Caddress_alternate_ext_ref_c%2C' .
            'address_primary_ext_ref_c%2Clast_updating_system_c%2Caccount_name_denormal%2Cfull_name%2Ctitle%2C' .
            'preferred_name_c%2Ckey_contact_c%2Ccontact_status_c%2Cprimary_address_street%2Cprimary_address_city%2' .
            'Cprimary_address_state%2Cprimary_address_country%2Cprimary_address_postalcode%2Caddress_suppressed%2C' .
            'supp_perm_c%2Cemail%2Cphone_work_suppressed%2Cphone_mobile_suppressed%2Cphone_code%2Csalutation%2C' .
            'alt_lang_first_c%2Calt_lang_last_c%2Calt_lang_preferred_name_c%2Caccount_id%2Cemail1%2Cmy_favorite%2C' .
            'following%2Cassigned_user_id&max_num=5&order_by=date_modified%3Adesc'
        );
        $request->addResource($ext);

        $request->getParser()->setExpectedDefaults();
        $request->getParser()->setExpected(
            'assertIndexOfArrayEquals',
            'records',
            array(
                'field' => 'id',
                'value' => $this->con->id
            )
        );

        $this->addRequest($request);

        $this->apiCall();
    }
}
