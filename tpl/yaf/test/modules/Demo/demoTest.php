<?php
//对DemoController的各个action测试
class DemoControllerTest extends TestCaseBase
{
    public function test_demoAction()
    {
        $expect  = "this is demo()";
        $request  = new Yaf_Request_Simple("", "demo", "demo", 'demo');
        $response = $this->_app->getDispatcher()
            ->returnResponse(true)
            ->dispatch($request);
        $content  = $response->getBody();
        $this->assertEquals($expect, $content);
    }
}
