<?php
/**
 * @desc 默认控制器
 * @author dzhcool
 * @date 2017-05-18
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */

class IndexController extends Yaf_Controller_Abstract{
    /**
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     */
    public function indexAction($name = 'default'){
        $get = $this->getRequest()->getQuery("get", "default value");

        /* //演示
         * $model = new UserModel();
         * if(empty(cache('data'))){
         *     $data = $model->where('status=1')->select();
         *     cache('data', $data);
         * }
         * dump(cache('data'));
         */

        $this->getView()->assign("name", $name);
        //render by Yaf, 如果这里返回FALSE, Yaf将不会调用自动视图引擎Render模板
        return true;
    }
}
