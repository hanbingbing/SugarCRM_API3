<?php

interface Api_Request_Interface{

    /**
     * @return string
     */
    public function getRequestType();

    /**
     * @return string
     */
    public function getPayloads();

    public function getPayloadsJSONString();

    public function getRESTfulURI();

}