<?php

class Api_Response_Parser_CurrentUserApi_retrieveCurrentUser extends Api_Response_Parser_Abstract
{

    public function setExpectedDefaults()
    {
        $this->setExpected('assertNotEmpty', 'current_user');
        $this->setExpected('assertNotEmpty', 'current_user.id');
    }
}