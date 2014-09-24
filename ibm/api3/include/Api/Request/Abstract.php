<?php

abstract class Api_Request_Abstract
{
    //define request type
    const TYPE_GET = 'get';
    const TYPE_POST = 'post';
    const TYPE_PUT = 'put';
    const TYPE_DELETE = 'delete';

    protected $reg;

    protected $url;

    protected $api_version;

    protected $rest_url = 'rest';

    protected $base_url;

    protected $request_type = Api_Request_Abstract::TYPE_GET;

    protected $resources = array();

    protected $payloads = array();

    protected $base_resources = array();

    protected $parser;

    protected $request_name;

    function __construct($base_resources = array(), $request_name = null)
    {
        $this->reg = App_Registry::getInstance();
        $this->url = $this->reg->get('sugar_url');
        $this->api_version = $this->reg->get('sugar_api_version');
        if (!empty($base_resources)) {
            $this->base_resources = $base_resources;
        }
        if (!empty($request_name)) {
            $this->request_name = $request_name;
        }
        $this->setParser();
    }

    /**
     * @param string $request_type
     */
    public function setRequestType($request_type)
    {
        $this->request_type = $request_type;
    }

    /**
     * @return string
     */
    public function getRequestType()
    {
        return $this->request_type;
    }

    /**
     * @param string $resource
     */
    public function addResource($resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * @param array $payloads
     */
    public function setPayloads($payloads)
    {
        $this->payloads = $payloads;
    }

    /**
     * @param string $payloads
     */
    public function setPayloadsJSON($payloads)
    {
        $this->payloads = (array)json_decode($payloads);
    }

    /**
     * @return string
     */
    public function getPayloads()
    {
        return $this->payloads;
    }

    public function getPayloadsJSONString()
    {
        return json_encode($this->payloads);
    }

    public function getBaseURL()
    {
        if (empty($this->base_url)) {
            // sugar_url+'rest'+'v10'
            $this->base_url = $this->url . '/' . $this->rest_url . '/' . $this->api_version;
        }

        return $this->base_url;

    }

    public function getRESTfulURI()
    {
        $uri = $this->getBaseURL();

        foreach ($this->base_resources as $resource) {
            if ($resource == '?') {
                $resource = array_shift($this->resources);
                if (get_class(current($this->resources)) == 'Api_Request_Fields') {
                    $fields = array_shift($this->resources);
                    $resource .= '?' . $fields->getFieldsString();
                }
            }
            $uri .= '/' . $resource;
        }

        foreach ($resources as $resource) {
            if (get_class($resources) == 'string') {
                $uri .= '/' . $resources;
            }
        }

        return $uri;
    }

    /**
     * @param Api_Response_Parser_Abstract $parser
     */
    public function setParser(Api_Response_Parser_Abstract $parser = null)
    {
        if (empty($parser)) {
            $requestClassName = get_class($this);
            if (!empty($this->request_name)) {
                $requestClassName = $this->request_name;
            }
            $parserClassName = 'Api_Response_Parser_' . str_replace('Api_Request_', '', $requestClassName);
            if(class_exists($parserClassName)){
                $this->parser = new $parserClassName();
            }else{
                $this->parser = new Api_Response_Parser_Abstract();
            }
        } else {
            $this->parser = $parser;
        }
    }

    /**
     * @return Api_Response_Parser_Abstract
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param null $request_name
     */
    public function setRequestName($request_name)
    {
        $this->request_name = $request_name;
    }

    /**
     * @return null
     */
    public function getRequestName()
    {
        return $this->request_name;
    }


}