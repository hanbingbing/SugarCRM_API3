<?php

/**
 * Class Api_Request_RelateRecordByCustomIdApi_fetchRelatedRecordByCustomId
 * GET rest/v10/<module>/<record>/link/<link_name>/field/<id_field>/<remote_id>
 */
class Api_Request_RelateRecordByCustomIdApi_fetchRelatedRecordByCustomId
    extends Api_Request_RelateRecordByCustomIdApi_createRelatedLinkByCustomId
{
    protected $request_type = Api_Request_Abstract::TYPE_GET;
    public function setParser(Api_Response_Parser_Abstract $parser = null)
    {
        if (empty($parser)) {
            $this->parser = new Api_Response_Parser_ModuleApi_retrieveRecord();
        } else {
            $this->parser = $parser;
        }
    }
}