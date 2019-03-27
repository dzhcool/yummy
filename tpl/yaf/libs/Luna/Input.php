<?php
class LInput
{
    private static function getRequest()
    {
        if (extension_loaded('yaf')) {
            $request = Yaf_Dispatcher::getInstance()->getRequest()->getRequest();
        } else {
            $request = $_REQUEST;
        }
        return $request ? $request : array();
    }

    public static function request()
    {
        return self::getData(func_get_args() , FALSE);
    }

    private static function getData($index, $fileter = FALSE)
    {
        $request = self::getRequest();
        $rst = array();
        if (is_array($index)) {
            foreach ($index as $v) {
                if (!empty($v)) {
                    $rst[$v] = self::getVar($v, $request, $fileter);
                }
            }
        } else {
            $rst = self::getVar($index, $request, $fileter);
        }
        return $rst;
    }

    public static function getVar($index, $request = NULL, $fileter = FALSE)
    {
        $request = $request === NULL ? self::getRequest() : $request;
        $var = isset($request[$index]) ? $request[$index] : NULL;
        return $fileter ? self::dhtmlspecialchars($var) : $var;
    }

    public static function getDefVar($index,$default = 0 )
    {
        $request = self::getRequest();
        $var     = isset($request[$index]) ? $request[$index] : $default;
        return   self::dhtmlspecialchars($var);
    }

    public static function getInt($index, $request = NULL){
        $request = $request === NULL ? self::getRequest() : $request;
        $var = isset($request[$index]) ? $request[$index] : NULL;
        return intval($var);
    }

    public static function getDefInt($index, $default = 0){
        $request = self::getRequest();
        $var     = isset($request[$index]) ? $request[$index] : $default;
        return intval($var);
    }

    private static function dhtmlspecialchars($string)
    {

        if (is_array($string)) {
            $rst = array();
            foreach ($string as $key => $val) {
                $rst[$key] = self::dhtmlspecialchars($val);
            }
        } else {
            $rst = str_replace(array(
                '&amp;',
                '&quot;',
                '&lt;',
                '&gt;'
            ) , array(
                '&',
                '"',
                '<',
                '>'
            ) , $string);
            $rst = str_replace(array(
                '&',
                '"',
                '<',
                '>'
            ) , array(
                '&amp;',
                '&quot;',
                '&lt;',
                '&gt;'
            ) , $rst);
        }
        return $rst;
    }
}

class LCheck
{
    public static function int($var, $errMsg, $min = null, $max = null)
    {
        $var = (int)$var;

        if (($min !== null && $var < $min) || ($max !== null && $var > $max)) {
            if (is_string($errMsg))
            {
                throw new Err_Input($errMsg);
            }
            else {
                throw $errMsg;
            }
        }
        return $var;
    }

    public static function string($var, $errMsg, $min = null, $max = null)
    {
        $varStr = (string)$var;
        $len = mb_strlen($varStr, 'utf8');
        if (($min !== null && $len < $min) || ($max !== null && $len > $max))
        {
            if (is_string($errMsg))
            {
                throw new Err_Input($errMsg);
            }
            else {
                throw $errMsg;
            }
        }
        return $varStr;
    }

    public static function __callStatic($name, $arguments)
    {
        if (substr($name, 0, 4) == 'arr_')
        {
            return self::arrayMethod($name, $arguments);
        }

        throw new Err_Svc('Unknown LCheck type');
    }

    protected static function arrayMethod($name, $arguments)
    {
        if (!isset($arguments[0]) || !isset($arguments[1]) || !isset($arguments[3]))
        {
            throw new Err_Svc('LCheck parameter error');
        }

        $min = (int) $arguments[0];
        $max = (int) $arguments[1];
        $arrayVar = isset($arguments[2]) ? $arguments[2] : NULL;
        $errMsg = $arguments[3];
        if (!is_array($arrayVar))
        {
            if ($min == 0)
            {
                return array();
            } else
            {
                throw new Err_Input($errMsg);
            }
        }
        if (($min > 0 && count($arrayVar) < $min) || ($max > 0 && count($arrayVar) > $max))
        {
            throw new Err_Input($errMsg);
        }

        $func = substr($name, 4);
        if (!method_exists(__CLASS__, $func))
        {
            throw new Err_Svc('Unknown LCheck type');
        }

        $arguments = array_slice($arguments, 2);
        $rst = array();
        foreach ($arrayVar as $k => $v)
        {
            $arguments[0] = $v;
            $rst[$k] = call_user_func_array(array(__CLASS__, $func), $arguments);
        }
        return $rst;
    }
}
