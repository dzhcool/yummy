<?php
/**
 * memcache操作类
 */
class CacheSvc
{
    private static $ins = null;
    private $mc = null;

    public static function ins(){
        if(empty(self::$ins)){
            self::$ins = new self;
        }
        return self::$ins;
    }

    public function __construct(){
        $this->logger = XLogKit::logger("_svc");

        $timeout = 1; //1s超时
        $mc = new  Memcache();
        $ret = $mc->connect($_SERVER['MEM_HOST'], $_SERVER['MEM_PORT'], $timeout);
        if($ret){
            $this->mc = $mc;
        }
    }

    public function __call($func, $params){
        $func = strtolower($func);
        if(empty($this->mc)){
            $this->logger->error("cache disabled", __CLASS__.'/'.__FUNCTION__);
            return "";
        }
        return call_user_func_array(array($this->mc, $func), $params);
    }
}
