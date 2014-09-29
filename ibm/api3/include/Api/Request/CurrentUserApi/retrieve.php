<?php

/**
 * Class Api_Request_CurrentUserApi_retrieve
 * GET rest/v10/me
 */
class Api_Request_CurrentUserApi_retrieve extends Api_Request_Abstract
{
    protected $base_resources = array('me');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}