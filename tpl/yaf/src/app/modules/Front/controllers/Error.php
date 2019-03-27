<?php
/**
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 */
class ErrorController extends Yaf_Controller_Abstract {

    // 异常处理
	public function errorAction($exception) {
        if (LunaRouter::isApi()) {
            Err_Handle::response($exception);
        } else {
            XlogKit::logger('error')->error($exception->getMessage());
            if ($exception instanceof LunaError)
            {
                $this->getView()->assign("errMsg", $exception->getMessage());
            }
            $this->getView()->display('common/error.tpl');
        }
	}
}
