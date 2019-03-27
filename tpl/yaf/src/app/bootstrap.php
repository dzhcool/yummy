<?php
/**
 * @name Bootstrap
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig() {
        //把配置信息保存到注册表
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //注册一个插件
        $dispatcher->registerPlugin(new Plugin_Request());
        $dispatcher->registerPlugin(new Plugin_Speed());
        $dispatcher->registerPlugin(new Plugin_Acl());
    }

    public function _initAutoLoad(Yaf_Dispatcher $dispatcher) {
        $loads = array(
            APPLICATION_PATH . '/library/vendor/autoload.php',
            $_SERVER['YAF_LIBS'] . '/vendor/autoload.php',
        );
        foreach($loads as $autoload)
        {
            if (file_exists($autoload)) {
                Yaf_Loader::import($autoload);
            }
        }
        // 手动加载一下自定义Exception和Luna相关类
        Yaf_Loader::getInstance()->autoload("Luna_Autoload");
    }

    public function _initServices(Yaf_Dispatcher $dispatcher) {
        $func = function($class){
            $loader = Yaf_Loader::getInstance(APPLICATION_PATH. "/services/");
            $loader->registerLocalNamespace($class);
            $e = explode('_', $class);
            if (count($e)>1)
            {
                $loader->registerLocalNamespace($e[0]);
            }
            $loader->autoload($class);
        };
        spl_autoload_register($func);
    }

    // 扩展service
    public function _initServiceExt(Yaf_Dispatcher $dispatcher) {
        $func = function($class){
            $class = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $class));
            $file = APPLICATION_PATH. "/services/".$class.'.php';
            if (file_exists($file)) {
                Yaf_Loader::import($file);
            }
        };
        spl_autoload_register($func);
    }

    public function _initUtil(Yaf_Dispatcher $dispatcher) {
        $func = function($class){
            if($class == 'UFun'){
                $class = 'Utils';
            }
            $file = APPLICATION_PATH. "/util/".$class.'.php';
            if (file_exists($file)) {
                Yaf_Loader::import($file);
            }
        };
        spl_autoload_register($func);
    }

    public function _initLog(Yaf_Dispatcher $dispatcher){
        XLogKit::conf($_SERVER['PRJ_NAME'], '&'.$_SERVER['SYS_NAME'], $_SERVER['LOG_LEVEL']);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        //在这里注册自己的路由协议,默认使用简单路由
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        $router->addRoute("lunarouter", new LunaRouter());
    }

    public function _initView(Yaf_Dispatcher $dispatcher){
        //在这里注册自己的view控制器，例如smarty,firekylin
        if (!LunaRouter::isApi()) {
            Yaf_Dispatcher::getInstance()->disableView();
            // define("VIEW_PATH", APPLICATION_PATH . '/modules/' . ucfirst($_SERVER['SYS_NAME']) . '/views/');
            // $dispatcher->initView(VIEW_PATH);
            //在这里注册自己的view控制器，例如smarty,firekylin
            Yaf_Loader::import(APPLICATION_PATH . '/../../libs/SmartyAdapter.php');
            Yaf_Loader::import(APPLICATION_PATH . '/util/Utils.php');
            $smarty = new SmartyAdapter(Yaf_Registry::get("config")->get("smarty"));
            $dispatcher->setView($smarty);
        }
    }

    public function _initJsonp(Yaf_Dispatcher $dispatcher){
        if (LunaRouter::isApi()) {
            header('Content-Type: application/x-javascript;charset=utf-8');
            $dispatcher->disableView();
        }
        if ($_REQUEST['callback']) {
            RestResult::ins()->setType('jsonp', $_REQUEST['callback']);
        }
    }
}
