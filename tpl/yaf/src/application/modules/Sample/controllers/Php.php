<?php
class PhpController extends Yaf_Controller_Abstract{
    public function infoAction(){
        //phpinfo();
        $body = "this is phpinfo()";
        $this->getResponse()->setBody($body);
        return false;
    }
}
