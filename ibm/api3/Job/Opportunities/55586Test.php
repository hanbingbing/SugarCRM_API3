<?php

class Job_Opportunities_55586Test extends App_Job_Abstract
{
    private $oppId = '5Y-PC6A98X';

    public function setup($params)
    {
        parent::setup($params);
    }

    public function cleanup($params)
    {
        parent::cleanup($params);
    }

    /**
     * @param array $params
     */
    public function run($params)
    {
        $this->setLoginUser('fh-level10user10', 'fh-level10user10');
        $this->apiEnableDumpResponse();

        $request = new Api_Request_ModuleApi_retrieve();
        $request->addResource('Opportunities'); //set module name
        $request->addResource($this->oppId); //set record id
        $ext = new Api_Request_RequestExtension();
        $ext->setExtensionsString('viewed=true&fields=name,account_name,ext_ref_id2_c,last_updating_system_c,' .
            'ext_ref_id1_c,assigned_user_name_template,related_opportunities,description,pcontact_id_c,amount,' .
            'sales_stage,date_closed,cmr_c,assigned_user_name,lead_source,solution_codes_c,tags,contact_id_c,' .
            'account_id,cmr_id,assigned_user_id,reason_won_c,reason_lost_c,my_favorite,following,currency_id,' .
            'closed_revenue_line_items,assigned_bp_id');
        $request->addResource($ext);

//        $this->addRequest($request);


        $request = new Api_Request_FilterApi_filterModuleGet();
        $request->addResource('Opportunities'); //set module name
        $ext = new Api_Request_RequestExtension();
        $ext->setExtensionsString('fields=name,account_name,ext_ref_id2_c,last_updating_system_c,ext_ref_id1_c,' .
            'assigned_user_name_template,related_opportunities,description,pcontact_id_c,amount,sales_stage,' .
            'date_closed,cmr_c,assigned_user_name,lead_source,solution_codes_c,tags,contact_id_c,account_id,' .
            'cmr_id,assigned_user_id,reason_won_c,reason_lost_c,my_favorite,following,currency_id,' .
            'closed_revenue_line_items,assigned_bp_id&max_num=5&order_by=date_modified:desc&my_items=1' .
            '&filter[0][$or][0][sales_stage]=01&filter[0][$or][1][sales_stage]=02&filter[0][$or][2][sales_stage]=03' .
            '&filter[0][$or][3][sales_stage]=04&filter[0][$or][4][sales_stage]=05&filter[0][$or][5][sales_stage]=06');
        //  .'&filter[1][id]=' . $this->oppId);

        $request->addResource($ext);

        $this->addRequest($request);

        $this->apiCall();
    }
}