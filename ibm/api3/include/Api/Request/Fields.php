<?php

class Api_Request_Fields
{
    protected $fields = array();

    public function addField($field)
    {
        $this->fields[] = $field;
    }

    public function getFieldsString()
    {
        return 'fields=' . implode(',', $this->fields);
    }
}