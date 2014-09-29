<?php

class Api_Request_RequestExtension
{
    protected $extensions = array();
    protected $extensionsString = '';

    public function addExtension($ext)
    {
        $this->extensions[] = $ext;
    }

    public function setExtensions($ext){
        $this->extensions = $$ext;
    }

    public function setExtensionsString($ext){
        if(is_string($ext)){
            $this->extensionsString = $ext;
        }
    }
    public function getExtensions(){
        if(!empty($this->extensionsString)) {
            return $this->extensionsString;
        }else{
            return implode("&",$this->extensions);
        }
    }
}