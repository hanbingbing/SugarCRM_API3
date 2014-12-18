<?php
/**
 * Class Api_Request_FilterApi_filterModuleGet
 * GET rest/v10/ibm_Products/filter
 */
class Api_Request_IbmProductsFilterApi_filterForAdjustments extends Api_Request_Abstract
{
    protected $base_resources = array('ibm_Products', 'filter');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}