<?php
/**
 * api interface
 */
interface Api_Interface
{
    public function login();

    public function logout();

    public function getAccessToken();

//    public function getLastRequest();
//
//    public function getLastResponse();
//
//    public function saveLastRequest($file);
//
//    public function saveLastResponse($file);
}
