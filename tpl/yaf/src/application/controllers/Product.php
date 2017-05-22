<?php
/**
 * @name ProductController
 * @author dzhcool
 * @desc 路由演示控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class ProductController extends Yaf_Controller_Abstract {

    public function viewAction($id = 0, $name = 'null') {
        echo "id:{$id}  name:{$name}";
        return false;
    }
}

