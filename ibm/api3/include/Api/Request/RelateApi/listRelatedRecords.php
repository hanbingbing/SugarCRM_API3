<?php

/**
 * Class Api_Request_RelateApi_listRelatedRecords
 * GET rest/v10/<module>/<record>/link/<link_name>
 */
class Api_Request_RelateApi_listRelatedRecords extends Api_Request_Abstract
{
    protected $base_resources = array('?', '?', 'link', '?');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}