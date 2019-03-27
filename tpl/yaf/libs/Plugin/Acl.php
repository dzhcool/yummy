<?php

class Plugin_Acl extends Yaf_Plugin_Abstract
{
    static $whiteAccess = array('index/login', 'index/logout', 'index/capcha', 'index/index', 'index/captcha');

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        @session_start();
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = md5(time(true).rand(1,9999));
        }
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        return; // 暂不验证授权
        $uid = $_SESSION['USER']['uid'];
        $url = strtolower($request->controller . '/' . $request->action);
        if (in_array($url, self::$whiteAccess))
        {
            return ;
        }

        if (empty($uid))
        {
            header("Location: /index/login");
            exit;
        }

        $res = $_SESSION['USER']['res'];
        if (!in_array($url, $res)) {
            //throw new Err_Acl('无权访问['.$url.']');
        }
    }
}
