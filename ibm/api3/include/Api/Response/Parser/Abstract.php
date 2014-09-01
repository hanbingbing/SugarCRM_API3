<?php

class Api_Response_Parser_Abstract
{
    public $debug = false;
    protected $parserName;
    protected $parentParserName;
    protected $responseObj;
    protected $expected = array();
    private $expect;
    public $passCount = 0;
    public $failCount = 0;
    public $failMessages = array();
    protected $errorCodeField = "error_code";
    protected $errorMsgField = "error_msg";
    protected $errorFeedbackField = "error_feedback";
    public $resultsStatusString = '';
    protected $tableField = 'module_name';
    public $cleanupSQL = array();
    public $parentModule;


    public function __construct($parserName = '')
    {
        $this->_jobName = '';
        $this->parserName = $parserName;
    }

    public function setJobName($jobName)
    {
        $this->_jobName = $jobName;
    }

    public function getParserName()
    {
        return $this->parserName;
    }

    public function setResponseObject($responseObj)
    {
        $this->responseObj = $responseObj;
    }

    public function getResponseObject()
    {
        return $this->responseObj;
    }

    /*
     * parse response.
     * find the appropriate parser, parse what you can, and then see if there
     * are embedded sub results/sub parsers, which would be called recursively
     */
    public function parseResponse($responseObj, $request, $parentresponseObject = '')
    {
        // go through expected results
        foreach ($this->expected as $expect) {
            $this->expect = $expect;
            $field = $expect['field'];
            $this->debugMsg("parseResult::found expected field: $field");
            if (method_exists($this, $expect['op'])) {
                $rc = $this->parseResultCallAssertion($responseObj, $expect);

                if ($rc) {
                    $this->debugMsg($expect['op'] . " pass for field $field");
                } else {
                    $this->debugMsg($expect['op'] . " fail for field $field");
                }
                if ($field == $this->errorCodeField) {
                    if ($rc) {
                        if (isset($responseObj->action) && $responseObj->action == 'create') {
                            $this->parseForCleanup($field, $responseObj, $parentresponseObject);
                        }
                        // on success, check for create actions so they can be cleaned up
                        $this->debugMsg("parseResult::field test pass: $field");
                    } else {
                        $this->debugMsg("parseResult::field test fail: $field");
                        // add any response errors to err msg list
                        $errMsgField = $this->errorMsgField;
                        if (isset($responseObj->$errMsgField)) {
                            $msg = "Error code message: " . $responseObj->$errMsgField;
                            $errFeedbackField = $this->errorFeedbackField;
                            if (isset($responseObj->$errFeedbackField)) {
                                $msg .= ": " . implode(', ', $responseObj->$errFeedbackField);
                            }
                            $this->failMessages[] = $this->_jobName . " : " . $msg;
                        }
                    }
                }

            } else {
                // failure bad config
                $this->failResult("assertion method does not exist: " . $expect['op']);
            }
        }
        $this->debugMsg("Looking for subresults: " . $this->parserName);
    }

    public function parseResultCallAssertion($responseObj, $expect)
    {
        $fields = explode('.', $expect['field']);
        $rc = true;
        $obj = $responseObj;

        foreach ($fields as $field) {
            if (preg_match("/\[(.*)\]/", $field, $match)) {
                $field = $match[1];
                if (!isset($obj[$field])) {
                    $this->failResult("field '$field' expected but does not exist in response");
                    $rc = false;
                    break;
                } else {
                    $obj = $obj[$field];
                }
            } else {
                if (!isset($obj->$field)) {
                    $this->failResult("field '$field' expected but does not exist in response");
                    $rc = false;
                    break;
                } else {
                    $obj = $obj->$field;
                }
            }
        }

        if ($rc) {
            $rc = $this->$expect['op']($obj, $expect['value'], $expect['failMsg']);
        }
        return $rc;
    }

    /*
     * try to clean up any created items
     */
    public function parseForCleanup($field, $responseObj, $parentresponseObject)
    {
        if (isset($responseObj->id)) {
            $id = $responseObj->id;
        }
        if (isset($responseObj->id_field)) {
            $idField = $responseObj->id_field;
        } else {
            $idField = 'id';
        }
        $tableField = $this->tableField;
        if (isset($responseObj->$tableField)) {
            $table = $responseObj->$tableField;
        }
        if (!empty($id) && !empty($table)) {
            $sql = "DELETE FROM $table WHERE $idField = '$id'";
            $this->cleanupSQL[] = $sql;
        }
    }

    /*
     *  emit debug messages
     */
    protected function debugMsg($msg)
    {
        if ($this->debug) {
            echo "DBG: $msg\n";
        }
    }

    /*
     * emit error messages
     */
    protected function errorMsg($msg)
    {
        echo "ERR: $msg\n";
    }

    public function validateModel($model)
    {
        foreach ($model as $k => $v) {
            $this->setExpected('assertEquals', $k, $v, "field mismatch");
        }
    }

    /*
     * meant to be overriden
     */
    public function setExpectedDefaults()
    {
    }

    /*
     * add an expected result
     */
    public function setExpected($op, $field, $value, $failMsg = '')
    {
        if (empty($failMsg)) {
            $failMsg = "$op failure on field '$field', expected '$value'";
        }
        $this->expected[] = array('op' => $op,
            'field' => $field,
            'value' => $value,
            'failMsg' => $failMsg);
        $this->debugMsg("setExpected $op on $field for $value");
    }

    private function updateFailMsg($failMsg, $function, $resultValue)
    {
        $msgParts = explode(' ', $failMsg);
        if ($msgParts[0] == $function) {
            $failMsg .= ", got '$resultValue'";
        }
        return $failMsg;
    }

    /*
     * assert given value and response value are equal
     */
    public function assertEquals($resultValue, $expectedValue, $failMsg = '')
    {
        if (is_array($expectedValue)) {
            foreach ($expectedValue as $key => $val) {
                $value = (isset($resultValue->$key)) ? $resultValue->$key : $resultValue[$key];
                $this->expect['field'] .= "['{$key}']";
                $failMsg = "assertEquals failure on field \"" . $this->expect['field'] . "\", expected '{$val}'";
                return $this->assertEquals($value, $val, $failMsg);
            }

        }
        if ($resultValue == $expectedValue) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $resultValue);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    /*
     * assert given value and response value are NOT equal
     */
    public function assertNotEquals($resultValue, $expectedValue, $failMsg = '')
    {
        if ($resultValue != $expectedValue) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $resultValue);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    /*
     * assert given response value is empty
     */
    public function assertEmpty($value, $failMsg = '')
    {
        if (empty($value)) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $value);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    /*
     * assert given response value is NOT empty
     */
    public function assertNotEmpty($value, $failMsg = '')
    {
        if (!empty($value)) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $value);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    /*
     * assert given response value start with expectedValue
     */
    public function assertStringStartsWith($resultValue, $expectedValue, $failMsg = '')
    {
        if (strpos($resultValue, $expectedValue) === 0) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $resultValue);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    public function assertLessThan($resultValue, $expectedValue, $failMsg = '')
    {
        if ($resultValue <= $expectedValue) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $resultValue);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    /*
     * assert given response value start with expectedValue
     */
    public function assertStringStartsNotWith($resultValue, $expectedValue, $failMsg = '')
    {
        if (strpos($resultValue, $expectedValue) !== 0) {
            $rc = $this->passResult();
        } else {
            $failMsg = $this->updateFailMsg($failMsg, __FUNCTION__, $resultValue);
            $rc = $this->failResult($failMsg);
        }
        return $rc;
    }

    public function assertIndexOfArrayEquals($resultValue, $expectedArray, $failMsg = '')
    {
        $failMsg = __FUNCTION__ . " failure on field '" .
            $expectedArray['field'] . "', expected " . $expectedArray['value'];

        foreach ($resultValue as $obj) {
            $fields = explode('.', $expectedArray['field']);
            $rc = true;
            foreach ($fields as $field) {
                if (preg_match("/\[(.*)\]/", $field, $match)) {
                    $field = $match[1];
                    if (!isset($obj[$field])) {
                        $rc = false;
                        break;
                    } else {
                        $obj = $obj[$field];
                    }
                } else {
                    if (!isset($obj->$field)) {
                        $rc = false;
                        break;
                    } else {
                        $obj = $obj->$field;
                    }
                }
            }

            if ($rc) {
                if($obj == $expectedArray['value']){
                    return $this->passResult();
                }else{
                    $rc = false;
                }
            }
        }
        if (!$rc) {
            $rc = $this->failResult($failMsg);
        }
    }

    public function assertEqualsInArray($list, $field, $value, $failMsg = '')
    {
        if (is_array($list)) {
            $match = false;
            foreach ($list as $item) {

                if (is_object($item)) {
                    if ($item->name == $field) {
                        if ($item->value == $value) {
                            $match = true;
                            break;
                        } else {
                            $failMsg .= ", got '" . $item->value . "'";
                        }
                    }
                }
            }
            if ($match) {
                $this->passResult();
            } else {
                $this->failResult($failMsg);
            }
            return $match;
        } else {
            #echo "BEGIN LIST " . $this->parserName . "\n";
            #print_r($list);
            #echo "END LIST\n";
            $this->failResult("result list not found for $field == $value");
            return false;
        }
    }

    public function assertNotEqualsInArray($list, $field, $value, $failMsg = '')
    {
        if ($expect['op'] == "assertEqualsInArray" || $expect['op'] == "assertNotEqualsInArray") {

            $rc = $this->$expect['op']($resultObj, $expect['field'], $expect['value'], $expect['failMsg']);
        }else{
            if (is_array($list)) {
                $match = false;
                foreach ($list as $item) {
                    if (is_object($item)) {
                        if ($item->name == $field && $item->value == $value) {
                            $match = true;
                            break;
                        }
                    }
                }
                if ($match) {
                    $this->failResult($failMsg);
                } else {
                    $this->passResult();
                }
            } else {
                $this->failResult("result list not found for $field != $value");
            }
        }
    }

    /*
     * process positive assertion
     */
    public function passResult()
    {
        if ($this->debug) {
            $this->resultsStatusString .= '.';
        } else {
            echo ".";
        }
        $this->passCount++;
        return true;
    }

    /*
     * process negative assertion
     */
    public function failResult($msg = '')
    {
        if ($this->debug) {
            $this->resultsStatusString .= "F";
        } else {
            echo "F";
        }
        $this->failCount++;
        $msg .= " in ";
        if (isset($this->parentParserName)) {
            $msg .= $this->parentParserName . "::";
        }
        $msg .= get_class($this);

        $this->failMessages[] = $this->_jobName . " : " . $msg;

        return false;
    }
}
