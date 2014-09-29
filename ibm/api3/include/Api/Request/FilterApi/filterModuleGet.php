<?php
/**
 * Class Api_Request_FilterApi_filterModuleGet
 * GET rest/v10/<module>/filter
 */
class Api_Request_FilterApi_filterModuleGet extends Api_Request_Abstract
{
    protected $base_resources = array('?', 'filter');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}