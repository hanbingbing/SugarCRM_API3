<?php

class Api_Response_Parser_RelateApi_listRelatedRecords extends Api_Response_Parser_Abstract
{
    public function setExpectedDefaults()
    {
        $this->setExpected('assertNotEmpty', 'records');
    }
}