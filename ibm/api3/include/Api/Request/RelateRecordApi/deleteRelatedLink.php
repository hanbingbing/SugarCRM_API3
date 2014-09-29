<?php

/**
 * Class Api_Request_RelateRecordApi_deleteRelatedLink
 * DELETE rest/v10/<module>/<record>/link/<link_name>/<remote_id>
 */
class Api_Request_RelateRecordApi_deleteRelatedLink
    extends Api_Request_RelateRecordApi_createRelatedLink
{
    protected $request_type = Api_Request_Abstract::TYPE_DELETE;
    public function setParser(Api_Response_Parser_Abstract $parser = null)
    {
        if (empty($parser)) {
            $this->parser = new Api_Response_Parser_RelateRecordApi_createRelatedLink();
        } else {
            $this->parser = $parser;
        }
    }
}