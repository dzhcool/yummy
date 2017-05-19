<?php
require_once(realpath(dirname(__FILE__)."/../src/application/library/startup.php"));

//单元测试基类，所有单测class都应该继承此类
class TestCaseBase extends PHPUnit_Framework_TestCase
{
    protected $_app = null;

    public function __construct()
    {
        $this->_app = $this->get_app();
        parent::__construct();
    }

    public function set_app()
    {
        $yaf = new YafEngine();
        $yaf->bootstrap();
        $app = $yaf->get_app();
        Yaf_Registry::set('app', $app);
    }

    public function get_app()
    {
        $app = Yaf_Registry::get('app');
        if (!$app)
        {
            $this->set_app();
        }
        return Yaf_Registry::get('app');
    }
}
