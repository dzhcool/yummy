<?php
define ( "DEBUG_LEVEL" , 0 );
define ( "INFO_LEVEL" , 1 );
define ( "WARN_LEVEL" , 2 );
define ( "ERROR_LEVEL" , 3 );

class XlogKit{
    private static $prj;
    private static $app;
    private static $out_level;
    private static $traceid;

    public static function conf($prj, $app, $level = INFO_LEVEL){
        self::$prj = $prj;
        self::$app = $app;
        self::$out_level = intval($level);
        self::getTraceId();
    }
    public static function getTraceId()
    {
        if (empty(self::$traceid)) {
            self::$traceid = uniqid();
        }
        return self::$traceid;
    }
    public static function logger($category){
        return new Xlogger(self::$prj, self::$app, $category, self::$out_level);
    }
}
class Xlogger{
    private $tag;
    private $ident;
    public function __construct($prj, $app, $category, $out_level){
        $env = ($_SERVER['ENV'] == "dev") ? substr($_SERVER['DOMAIN_PREFIX'], 0, -1) : $_SERVER['ENV'];
        $this->tag = "tag[{$env},$app]";
        $this->ident = "{$prj}/{$category}";

        $this->prj = $prj;
        $this->app = $app;
        $this->out_level = $out_level;
    }

    public function debug($string, $evt = ""){
        return $this->write($string, DEBUG_LEVEL, $evt);
    }

    public function info($string, $evt = ""){
        return $this->write($string, INFO_LEVEL, $evt);
    }

    public function warn($string, $evt = ""){
        return $this->write($string, WARN_LEVEL, $evt);
    }

    public function error($string, $evt = ""){
        return $this->write($string, ERROR_LEVEL, $evt);
    }

    private function write($string, $level, $evt = ""){
        if ($this->out_level > $level) return false;
        if(!function_exists('syslog')) return false;
        switch($level){
            case 0:
                $log_le = LOG_DEBUG;
                $log_msg = "[debug]";
                break;
            case 1:
                $log_le = LOG_INFO;
                $log_msg = "[info]";
                break;
            case 2:
                $log_le = LOG_WARNING;
                $log_msg = "[warn]";
                break;
            case 3:
                $log_le = LOG_ERR;
                $log_msg = "[error]";
                break;
            default:
                $log_le = LOG_INFO;
                $log_msg = "[info]";
        }
        openlog($this->ident, LOG_PID, LOG_LOCAL6);

        $result = @syslog($log_le, "{$this->tag} evt[{$evt}] [".XlogKit::getTraceId()."] {$log_msg} {$string}");
        closelog();
        return $result;
    }
}
