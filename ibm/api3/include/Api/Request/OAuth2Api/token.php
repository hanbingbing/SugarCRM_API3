<?php

class Api_Request_OAuth2Api_token extends Api_Request_Abstract
{
    protected $base_resources = array('oauth2','token');

    protected $request_type = Api_Request_Abstract::TYPE_POST;
}