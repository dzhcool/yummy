<?php
/**
 * Yaf 自定义路由协议
 * @version  0.1
 */

class LunaRouter  implements Yaf_Route_Interface{

    /**
     * Route 实现，继承实现Yaf_Router_Interface route
     * @param  Object(Yaf_Request_Http) $req 默认参数
     * html路径 /test/index
     * api路径  /api/test/info
     */
    public function route ($req){
        list($request_uri, $args) = explode('?', $_SERVER['REQUEST_URI']);
        $uri = explode('/', trim($request_uri,'/'));
        $req->module = $_SERVER['SYS_NAME'];
        if($uri[0] == 'api'){
            $req->controller = !empty($uri[1]) ? 'api_'.$uri[1] : '';
            $req->action = !empty($uri[2]) ? $uri[2] : '';
        } else {
            $req->controller = !empty($uri[0]) ? $uri[0] : '';
            $req->action = !empty($uri[1]) ? $uri[1] : '';
        }
        // if(!empty($args)) {
        //     $param = array();
        //     $params = explode('&', $args);
        //     foreach ( $params as $value) {
        //         $arr = explode('=', $value);
        //         if (count($arr)==2) {
        //             $param[$arr[0]] = $arr[1];
        //         }
        //     }
        //     $req->params = $param;
        // }

        // var_dump('module:'.$req->module, 'controller:'.$req->controller, 'action:'.$req->action, $req->params);exit;
        return true;
    }

    public function assemble (array $info, array $query = NULL){
        return true;
    }

    static public function isApi() {
        $uri = explode('/', trim(str_replace('_', '/', $_SERVER['REQUEST_URI']), '/'));
        return $uri[0] == 'api' || $_REQUEST['__luna_call'] ? true : false;
    }
}
