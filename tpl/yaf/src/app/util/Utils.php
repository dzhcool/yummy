<?php
class Utils
{
    // 获取客户端IP
    public static function getClientIP()
    {
        $ip = 'Unknow';

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            return self::isIP($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $ip;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            return self::isIP($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $ip;
        }
        else
        {
            return self::isIP($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $ip;
        }
    }

    private static function isIP($str)
    {
        $ip = explode('.',$str);
        for ($i=0; $i<count($ip); $i++)
        {
            if ($ip[$i] > 255)
            {
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
    }
    public static function xhprof() {
        if ($_SERVER['XHPROF']) {
            $xhprofData = xhprof_disable();
            include_once "/data/xhprof/xhprof_lib/utils/xhprof_lib.php";
            include_once "/data/xhprof/xhprof_lib/utils/xhprof_runs.php";
            $xhprofRuns = new XHProfRuns_Default();
            $spentTime = intval($xhprofData['main()']['wt'] / 1000);
            if ($spentTime > 500) {
                $runid = $xhprofRuns->save_run($xhprofData, "xhprof");
                XLogKit::logger("xhprof")->info("/xhprof/callgraph.php?run={$runid} " . $spentTime . 'ms');
            }
        }
    }
}

/**
 * @UFun 自定义函数
 * @author dangzihao
 */
class UFun
{
    //AES加密配置
    const CY_AES_MODE = MCRYPT_MODE_ECB;
    const CY_AES_CIPHER = MCRYPT_RIJNDAEL_128;

    // 系统执行时间
    public static function microtime_float(){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    // 系统执行时间
    public static function microtime_run($len=4) {
        $StartTime = (empty($GLOBALS['StartTime'])) ? self::microtime_float() : $GLOBALS['StartTime'];
        $EndTime = self::microtime_float();
        $RunTime = sprintf("%.{$len}f",$EndTime-$StartTime);
        return $RunTime;
    }
    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @return mixed
     */
    public static function get_client_ip($type=0){
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
    public static function hex2bin($str) {
        return pack('H' . strlen($str), $str);
    }
    public static function input_val($val=''){
        if(strtoupper($val) != 'NULL' && strtoupper($val) != 'UNDEFINED' && !empty($val)){
            $val = htmlspecialchars($val);
            $val = addslashes($val);
            $val = preg_replace('/\+\/v/i',"",$val);
        }
        return $val;
    }
    public static function input($input=array()){
        if(!is_array($input)) return self::input_val($input);
        foreach($input as $key=>$row){
            if(is_array($row)){
                $input[$key] = self::input($row);
            }else{
                $input[$key] = self::input_val($row);
            }
        }
        return $input;
    }
    public static function mkdirs($dir, $mode = 0777){
        if (!is_dir($dir)) {
            self::mkdirs(dirname($dir), $mode);
            return mkdir($dir, $mode);
        }
        return true;
    }
    //判断是否局域网ip
    public static function is_lan($ip){
        return (($ip & 0xff000000) == 0x0a000000   || ($ip & 0xfff00000) == 0xac100000  || ($ip & 0xffff0000) == 0xc0a80000);
    }
    public static function curl_get_contents($url = '', $method = "GET", $data = array(),$cookie='',$timeout=30) {
        if(empty($data)) $data = array();
        if(empty($cookie)) $cookie = '';
        $query  = array();
        $curl   = curl_init();
        foreach($data as $k=>$v){
            $query[] = $k.'='.urlencode($v);
        }
        if(strtoupper($method) == 'GET' && $data){
            $url .= '?'.implode('&', $query);
        }elseif(strtoupper($method) == 'POST' && $data){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, implode('&', $query));
        }
        if(!empty($cookie)) curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    public static function passwd($string, $key = ""){
        $sha = sha1($string, true);
        $passwd = md5("{$key}|{$sha}|{$string}");
        return $passwd;
    }

    public static function jump($url, $alert = ''){
        if(!empty($alert)){
            echo "<script>alert('".$alert."');</script>";
        }
        echo '<meta http-equiv="refresh" content="0;url='.$url.'">';
        exit();
    }
    public static function encode($array = array()){
        return serialize($array);
    }
    public static function decode($string = ""){
        return unserialize($string);
    }

    public static function AESEncode($val, $key) {
        $val = str_pad($val, (16 * (floor(strlen($val) / 16) + 1)), chr(16 - (strlen($val) % 16)));
        return bin2hex(mcrypt_encrypt(self::CY_AES_CIPHER, $key, $val, self::CY_AES_MODE, mcrypt_create_iv(mcrypt_get_iv_size(self::CY_AES_CIPHER, self::CY_AES_MODE),MCRYPT_RAND)));
    }

    public static function AESDecode($val, $key) {
        return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', mcrypt_decrypt(self::CY_AES_CIPHER, $key, hex2bin($val), self::CY_AES_MODE,
            mcrypt_create_iv(mcrypt_get_iv_size(self::CY_AES_CIPHER, self::CY_AES_MODE), MCRYPT_RAND)));
    }

    // 短连接生成
    public static function short($long_url, $idx = 0) {
        $base32 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $key = "muwai";
        $hex = md5($key.$long_url);
        $hexLen = strlen( $hex );
        $output = array();
        for($i=0;$i<4;$i++) {
            $subHex = substr($hex, $i*$hexLen/4,$hexLen/4);
            $int = hexdec($subHex) & 0x3fffffff;
            $out = '';
            for ($j = 0; $j < 6; $j++) {
                $val = $int & 0x0000003d;
                $out .= $base32[$val];
                $int = $int >> 5;
            }
            $output[] = $out;
        }
        return $output[$idx];
    }

}
