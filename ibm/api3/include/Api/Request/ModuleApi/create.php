<?php

/**
 * Class Api_Request_ModuleApi_create
 * POST rest/v10/<module>
 */
class Api_Request_ModuleApi_create extends Api_Request_Abstract
{
    protected $base_resources = array('?');

    protected $request_type = Api_Request_Abstract::TYPE_POST;

}