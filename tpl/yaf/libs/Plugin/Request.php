<?php

class Plugin_Request extends Yaf_Plugin_Abstract
{
    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        if (LunaRouter::isApi()) {
            header('Content-Type: application/x-javascript;charset=utf-8');
            Yaf_Dispatcher::getInstance()->disableView();
            if ($_REQUEST['callback']) {
                RestResult::ins()->setType('jsonp', $_REQUEST['callback']);
            }
        }
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $params = $_REQUEST;

        if (empty($params)) {
            return ;
        }

        foreach ($params as $key => $value) {
            if (is_string($value))
            {
                $request->setParam($key, htmlspecialchars($value));
            }
        }
    }
}
