<?php

/**
 * Class Api_Request_RelateApi_filterRelatedRecords
 * GET rest/v10/<module>/<record>/link/<link_name>/filter
 */
class Api_Request_RelateApi_filterRelatedRecords extends Api_Request_Abstract
{
    protected $base_resources = array('?', '?', 'link', '?', 'filter');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}