<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__)."/../"));
define('YAF_CONF_PATH', realpath(APPLICATION_PATH."/../../conf/used/"));
define('YAF_INI_SUFFIX', '_yaf.ini');

class YafEngine
{
    private $_app_ins        = null;
    private $_load_bootstrap = true;

    public function __construct()
    {
        $this->_check_yaf_so();
        $ini = $this->_get_ini_file();
        $this->_app_ins = new Yaf_Application($ini);
    }

    public function disable_bootstrap()
    {
        $this->_load_bootstrap = false;
    }

    public function bootstrap()
    {
        $this->_app_ins->bootstrap();
    }

    public function run()
    {
        if($this->_load_bootstrap)
        {
            $this->_app_ins->bootstrap()->run();
        }
        else
        {
            $this->_app_ins->run();
        }

    }

    public function get_app()
    {
        return $this->_app_ins;
    }

    //检查yaf扩展是否加载
    private function _check_yaf_so()
    {
        if (!extension_loaded('yaf')) {
            echo "extension [yaf.so] absent!";
            exit(-1);
        }
    }

    //获取ini文件全路径
    private function _get_ini_file()
    {
        $sys_name = getenv('SYS_NAME');
        $ini_name = $sys_name.YAF_INI_SUFFIX;
        $ini      = YAF_CONF_PATH."/".$ini_name;

        if (!file_exists($ini)) {
            echo "[ini]:$ini is not exists!";
            exit(-2);
        }

        return $ini;
    }
}
