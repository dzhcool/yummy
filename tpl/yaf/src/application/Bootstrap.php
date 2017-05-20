<?php
/**
 * @name Bootstrap
 * @author dzhcool
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initCommon(){
        $commonFile = $_SERVER['PRJ_ROOT']."/src/application/common/function.php";
        //加载公共函数库
        if(file_exists($commonFile)){
            require_once $commonFile;
        }
        //加载语言包
        $langFile   = $_SERVER['PRJ_ROOT']."/src/application/lang/zh_cn.php";
        if(file_exists($langFile)){
            Lang(include $langFile);
        }
    }

    public function _initConfig() {
        //注册配置文件
        $config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $config);
        //开启缓存
        if($config->application->cache->open){
            Register::_set('cache',Cache::getInstance());
        }
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        //注册一个插件
        $objSamplePlugin = new SamplePlugin();
        $dispatcher->registerPlugin($objSamplePlugin);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        //添加配置文件中的路由
        $router->addConfig(Yaf_Registry::get("config")->routes);
        //自定义路由测试
        $route = new Yaf_Route_Rewrite(
            'product/:id',
            array(
                'module' => 'Index',
                'controller' => 'Product',
                'action' => 'view'
            )
        );
        //使用路由器装载路由协议
        $router->addRoute('rewrite', $route);
    }

    public function _initLoader(){
        //注册本地类库
        Yaf_Loader::getInstance()->registerLocalNamespace(array("Db"));
    }

    public function _initView(Yaf_Dispatcher $dispatcher){
        //在这里注册自己的view控制器，例如smarty,firekylin
    }
}
