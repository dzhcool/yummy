<?php

class Plugin_Speed extends Yaf_Plugin_Abstract
{
    static $btime;
    static $etime;

    static public function start()
    {
        self::$btime = microtime(true);
    }

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        self::start();
    }

    // 有异常时执行不到这里
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $btime = self::$btime;
        $etime = microtime(true);
        $usetime=sprintf("%.3f", $etime-$btime);
        XLogKit::logger("_speed")->info(" [usetime:{$usetime}(s)] ".$_SERVER['REQUEST_URI']);
    }

    // 发生异常时，记录一下脚本执行时间
    static public function errorEnd()
    {
        $btime = self::$btime;
        $etime = microtime(true);
        $usetime=sprintf("%.3f", $etime-$btime);
        XLogKit::logger("_speed")->info(" [usetime:{$usetime}(s)] ".$_SERVER['REQUEST_URI']);
    }
}
