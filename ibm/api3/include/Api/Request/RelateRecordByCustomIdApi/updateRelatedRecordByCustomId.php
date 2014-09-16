<?php

/**
 * Class Api_Request_RelateRecordByCustomIdApi_updateRelatedRecordByCustomId
 * PUT rest/v10/<module>/<record>/link/<link_name>/field/<id_field>/<remote_id>
 */
class Api_Request_RelateRecordByCustomIdApi_updateRelatedRecordByCustomId
    extends Api_Request_RelateRecordByCustomIdApi_createRelatedLinkByCustomId
{
    protected $request_type = Api_Request_Abstract::TYPE_PUT;
    public function setParser(Api_Response_Parser_Abstract $parser = null)
    {
        if (empty($parser)) {
            $this->parser = new Api_Response_Parser_RelateRecordApi_createRelatedLink();
        } else {
            $this->parser = $parser;
        }
    }
}