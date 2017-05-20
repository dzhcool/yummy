<?php
/*
 * 这里是对数据模型models的测试
 */


//对SampleModel的各个接口测试
class SampleTest extends TestCaseBase
{
    public function test_selectSameple()
    {
        $sm  = new SampleModel();
        $ret = $sm->selectSample();
        $this->assertTrue(true);
    }
}
