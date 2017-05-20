<?php
//对PhpController的各个action测试
class PhpControllerTest extends TestCaseBase{
    public function test_infoAction(){
        $expect  = "this is phpinfo()";
        $request  = new Yaf_Request_Simple("", "Sample", "Php", 'info');
        $response = $this->_app->getDispatcher()
            ->returnResponse(true)
            ->dispatch($request);
        $content  = $response->getBody();
        // $this->assertEquals($expect, $content);
        $this->assertTrue( !empty($content) );
    }
}
