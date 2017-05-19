<?php
class DemoController extends Yaf_Controller_Abstract{
    public function demoAction(){
        $body = "this is demo()";
        $this->getResponse()->setBody($body);
        return false;
    }
}
