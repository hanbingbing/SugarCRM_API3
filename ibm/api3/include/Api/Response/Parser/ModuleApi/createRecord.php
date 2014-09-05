<?php

class Api_Response_Parser_ModuleApi_createRecord extends Api_Response_Parser_Abstract
{

    public function setExpectedDefaults()
    {
        $this->setExpected('assertNotEmpty', 'id');
    }

    public function parseForCleanup($responseObj, $parentresponseObject)
    {
        $id = $responseObj->id;
        $module = $responseObj->_module;

        if(!empty($module) && !empty($module)){
            $bean = BeanFactory::getBean($module);
            $table = $bean->table_name;
            $sql = "DELETE FROM {$table} WHERE id = '{$id}'";
            $this->cleanupSQL[] = $sql;
        }
    }

}
