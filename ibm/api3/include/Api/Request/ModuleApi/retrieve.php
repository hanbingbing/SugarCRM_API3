<?php

/**
 * Class Api_Request_ModuleApi_retrieve
 * GET rest/v10/<module>/<record>
 */
class Api_Request_ModuleApi_retrieve extends Api_Request_Abstract
{
    protected $base_resources = array('?', '?');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}