<?php
// 访问路径 /api_demo/show
class Api_DemoController extends LAction
{
    // 用户列表
    public function show(){
        $rst = [
            'name' => 'dzhcool',
            'email' => 'xxx@gmail.com',
        ];

        return $this->success($rst);
    }
}
