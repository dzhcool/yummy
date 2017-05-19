<?php
/*
 * 这里是对模块modules的测试
 */


//对IndexController的各个action测试
class IndexControllerTest extends TestCaseBase
{
    public function test_indexAction()
    {
        $name     = 'dzhcool';
        $request  = new Yaf_Request_Simple("", "index", "index", 'index', array('name' => $name));
        $response = $this->_app->getDispatcher()
            ->returnResponse(true)
            ->dispatch($request);
        $content  = $response->getBody();
        $this->assertEquals('name:'.$name, $content);
    }
}
