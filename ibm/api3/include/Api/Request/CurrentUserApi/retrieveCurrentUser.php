<?php

class Api_Request_CurrentUserApi_retrieveCurrentUser extends Api_Request_Abstract
{
    protected $base_resources = array('me');

    protected $request_type = Api_Request_Abstract::TYPE_GET;

}