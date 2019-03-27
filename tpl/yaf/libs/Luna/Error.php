<?php
class Err_Code
{

    const UNAUTHORIZED          = 50401;    //当前用户没有服务授权(没登陆,或者已登录但没有授权当前服务)
    const FORBIDDEN             = 50403;    //权限验证失败
    const NOT_FOUND             = 50404;    //Controller不存在
    const AUTHREQUIRED          = 50407;    //需要激活授权(账号不存在或未激活)
    const PRECONDITION_FAILED   = 50412;    //外部输入错误(例如:参数不合法 ID不存在)
    const INPUT_EXT             = 50413;    //外部输入错误(需前端处理)
    const INTERNAL_SERVER_ERROR = 50500;    //内部错误
    const BAD_GATEWAY           = 50502;    //依赖服务或资源错误
    const GATEWAY_TIMEOUT       = 50504;    //依赖服务或资源超时

    private static $_messages = array(
        50401 => 'Unauthorized',
        50403 => 'Forbidden',
        50404 => 'Not Found',
        50407 => 'Authentication Required',
        50412 => 'Input Error',
        50413 => 'Input Error Ext',
        50500 => 'Server Error',
        50502 => 'Bad Gateway',
        50504 => 'Gateway Timeout'
    );

    public static function getMessage($code)
    {
        return self::isUnknownCode($code) ? 'Unknown Error' : self::$_messages[$code];
    }

    public static function isUnknownCode($code)
    {
        return !isset(self::$_messages[$code]);
    }

}

abstract class LunaError extends Yaf_Exception
{

    abstract function getErrLevel();

    abstract protected function formatAlert();

    public function __construct($message = null, $code = null, $previous = null)
    {
        $code = static::ERR_CODE;
        if (Err_Code::isUnknownCode($code))
        {
            $code = Err_Code::INTERNAL_SERVER_ERROR;
        }

        $message = trim($message);
        if (empty($message))
        {
            $message = Err_Code::getMessage($code);
        }
        $log_type = $this->getErrLevel();
        if ($log_type == 'error')
        {
            LLog::error($message, 2);
            // XLogKit::logger("_exception")->error(" [usetime:{$usetime}(s)] ".$_SERVER['REQUEST_URI']);
        } else
        {
            LLog::info($message, TRUE, 2);
            // XLogKit::logger("_exception")->info(" [usetime:{$usetime}(s)] ".$_SERVER['REQUEST_URI']);
        }
        parent::__construct($message, $code, $previous);
    }

    public function getAlert()
    {
        if ($this->getCode() == Err_Code::INPUT_EXT)
        {
            return $this->formatAlert();
        } else
        {
            return $this->formatAlert() . self::getTraceTag();
        }
    }

    public static function getTraceTag()
    {
        return '[' . date('YmdHis') . '-' . XLogKit::getTraceId() . ']';
    }

    public static function rethrow($errMessage, $errCode)
    {
        switch ($errCode)
        {
            case '50401':
                $cls = 'Err_Auth';
                break;
            case '50403':
                $cls = 'Err_Acl';
                break;
            case '50404':
                $cls = 'Err_404';
                break;
            case '50407':
                $cls = 'Err_Account';
                break;
            case '50412':
                $cls = 'Err_Input';
                break;
            case '50413':
                $cls = 'Err_InputExt';
                break;
            case '50500':
                $cls = 'Err_Svc';
                break;
            case '50502':
                $cls = 'Err_Res';
                break;
            case '50504':
                $cls = 'Err_ResTimeout';
                break;
            default :
                $cls = NULL;
                break;
        }
        if ($cls)
        {
            throw new $cls($errMessage);
        } else
        {
            throw new Exception($errMessage, $errCode);
        }
    }

}

/**
 * 身份验证失败 [50401]
 */
class Err_Auth extends LunaError
{

    const ERR_CODE = Err_Code::UNAUTHORIZED;

    protected function formatAlert()
    {
        return '请先登录';
    }

    public function getErrLevel()
    {
        return 'info';
    }

}

/**
 * 账号状态异常 [50407]
 */
class Err_Account extends LunaError
{

    const ERR_CODE = Err_Code::AUTHREQUIRED;

    protected function formatAlert()
    {
        $msg = $this->getMessage();
        return $msg ? $msg : '账号不可用';
    }

    public function getErrLevel()
    {
        return 'info';
    }

}

/**
 * 权限验证失败 [50403]
 */
class Err_Acl extends LunaError
{

    const ERR_CODE = Err_Code::FORBIDDEN;

    protected function formatAlert()
    {
        return '权限认证失败:' . $this->getMessage();
    }

    public function getErrLevel()
    {
        return 'info';
    }

}

/**
 * 无法找到请求资源 [50404]
 */
class Err_404 extends LunaError
{

    const ERR_CODE = Err_Code::NOT_FOUND;

    protected function formatAlert()
    {
        return '无法找到资源:' . $this->getMessage();
    }

    public function getErrLevel()
    {
        return 'info';
    }

}

/**
 * 外部输入错误 [50412]
 */
class Err_Input extends LunaError
{

    const ERR_CODE = Err_Code::PRECONDITION_FAILED;

    protected function formatAlert()
    {
        return $this->getMessage();
    }

    public function getErrLevel()
    {
        return 'info';
    }

}

/**
 * 外部输入错误(需前端处理) [50413]
 */
class Err_InputExt extends LunaError
{

    const ERR_CODE = Err_Code::INPUT_EXT;

    protected function formatAlert()
    {
        return $this->getMessage();
    }

    public function getErrLevel()
    {
        return 'info';
    }

}

/**
 * 服务错误 [50500]
 */
class Err_Svc extends LunaError
{

    const ERR_CODE = Err_Code::INTERNAL_SERVER_ERROR;

    protected function formatAlert()
    {
        return '系统错误:' . $this->getMessage();
    }

    public function getErrLevel()
    {
        return 'error';
    }

}

/**
 * 资源错误 [50502]
 */
class Err_Res extends LunaError
{

    const ERR_CODE = Err_Code::BAD_GATEWAY;

    protected function formatAlert()
    {
        $errMsg = $this->getMessage();
        $errMsg = empty($errMsg) ? '服务或资源请求失败' : $errMsg;
        return $errMsg;
    }

    public function getErrLevel()
    {
        return 'error';
    }

}

/**
 * 资源超时 [50504]
 */
class Err_ResTimeout extends LunaError
{

    const ERR_CODE = Err_Code::GATEWAY_TIMEOUT;

    protected function formatAlert()
    {
        return '服务或资源请求超时';
    }

    public function getErrLevel()
    {
        return 'error';
    }

}

/**
 * 异常处理
 */
class Err_Handle
{

    static public function response($e)
    {
        $errCode = $e->getCode();
        $errCode = empty($errCode) ? Err_Code::INTERNAL_SERVER_ERROR : $errCode;
        if($_REQUEST['__luna_call'] == 'luna')
        {
            $errMessage = $e->getMessage();
        }  else
        {
            if ($e instanceof Yaf_Exception)
            {
                $errMessage = self::getYafMessage($e->getMessage(), $e->getCode()) . LunaError::getTraceTag();
            }
            else
            {
                $errMessage = ($e instanceof LunaError) ? $e->getAlert() : ($e->getMessage() . LunaError::getTraceTag());
            }
        }

        RestResult::ins()->error($errMessage, $errCode)->show();

        // 异常时也记录一下处理时长
        if (extension_loaded('yaf'))
        {
            Plugin_Speed::errorEnd();
        }
    }

    static private function getYafMessage($message, $code)
    {
        $online = $_SERVER['ENV'] == 'online' ? true : false;
        if ($online)
        {
            switch ($code)
            {
            case YAF_ERR_STARTUP_FAILED:
                return 'yaf startup failed';
            case YAF_ERR_CALL_FAILED:
                return 'yaf call failed';
            case YAF_ERR_AUTOLOAD_FAILED:
                return 'yaf autoload failed';
            case YAF_ERR_TYPE_ERROR:
                return 'yaf type failed';
            case YAF_ERR_ROUTE_FAILED:
                return 'yaf route failed';
            case YAF_ERR_DISPATCH_FAILED:
                return 'yaf dispatch failed';
            case YAF_ERR_NOTFOUND_MODULE:
                return 'not found module';
            case YAF_ERR_NOTFOUND_CONTROLLER :
                return 'not found controller';
            case YAF_ERR_NOTFOUND_ACTION:
                return 'not found action';
            case YAF_ERR_NOTFOUND_VIEW:
                return 'not found view';
            }
        }
        return $message;
    }
}

/**
 * 带有trace信息的异常日志
 */
class LLog
{
    public static function getLogger()
    {
        return new LunaLog();
    }

    public static function info($data, $isTrace = FALSE, $traceSeek = 1)
    {
        $logger = XLogKit::logger('info');

        $msg = self::formatData($data);
        if ($isTrace)
        {
            $backtrace = array_slice(debug_backtrace(), $traceSeek, 3, TRUE);
            $msg = self::formatTrace($backtrace) . $msg;
        }
        $logger->info($msg);
    }

    public static function error($data, $traceSeek = 1)
    {
        $logger = XLogKit::logger('error');

        $backtrace = array_slice(debug_backtrace(), $traceSeek, 3, TRUE);
        $msg = self::formatTrace($backtrace) . self::formatData($data);
        $logger->error($msg);
    }

    public static function debug($data, $isTrace = FALSE, $traceSeek = 1)
    {
        $logger = XLogKit::logger('debug');

        $msg = self::formatData($data);
        if ($isTrace)
        {
            $backtrace = array_slice(debug_backtrace(), $traceSeek, 3, TRUE);
            $msg = self::formatTrace($backtrace) . $msg;
        }
        $logger->debug($msg);
    }

    protected static function formatData($data)
    {
        //todo:变量长度需要限制
        $rst = $data;
        if (is_array($data) || is_object($data))
        {
            if (is_array($data) || $data instanceof stdClass)
            {
                $rst = '[' . urldecode(http_build_query($data, 'VAR_', '&')) . ']';
            } else
            {
                $rst = get_class($data);
            }
        }
        return $rst;
    }

    protected static function formatTrace($backtrace)
    {
        $trace = ' TRACE[';
        foreach ($backtrace as $k => $v)
        {
            $file = empty($v['file']) ? '*' : basename($v['file']) . "@{$v['line']}";
            $trace .= " #$k.<{$file}>";
            if (!empty($v['class']))
            {
                $trace .= $v['class'] . $v['type'];
            }
            $args = array();
            if (!empty($v['args']))
            {
                foreach ($v['args'] as $x)
                {
                    $args[] = self::formatData($x);
                }
            }
            $args = implode(',', $args);
            $trace .= "{$v['function']}({$args})";
        }
        $trace .= '] ';
        return $trace;
    }

}

class LunaLog implements IGLogger
{
    public function __construct()
    {
        ;
    }

    public function info($data)
    {
        LLog::info($data);
    }

    public function error($data)
    {
        LLog::error($data);
    }

    public function debug($data)
    {
        LLog::debug($data);
    }

}

interface IGLogger
{
    public function info($msg) ;
    public function error($msg) ;
    public function debug($msg) ;
}

