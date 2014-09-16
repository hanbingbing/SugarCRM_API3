<?php

class Api_Response_Parser_RelateRecordApi_createRelatedLink extends Api_Response_Parser_Abstract
{
    public function setExpectedDefaults()
    {
        $this->setExpected('assertNotEmpty', 'record');
        $this->setExpected('assertNotEmpty', 'related_record');
    }
}