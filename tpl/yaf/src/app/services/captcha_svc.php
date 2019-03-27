<?php
/**
 * 验证码服务
 * @author dangzihao
 * @date 2016-01-10
 */
class CaptchaSvc
{
    private static $ins = null;

    private $codeLen; //验证码字符长度
    private $codeType; //验证码字符类型
    private $app;      //接入方
    private $width;
    private $height;
    private $isBorder;
    private $level = 1; //验证码级别
    private $levelExpire = 60 * 60; //秒，默认1小时
    private $codeExpire = 10 * 60; //秒，验证码有效期，10分钟


    //leve禁用级别
    const DISABLE_LEVEL = 9999;

    //字符类型
    const CHAR_NUMBER = 1;
    const CHAR_LETTER = 2;
    const CHAR_MIXTURE = 3;

    //加密参数
    const SP = "*|*";
    const AES_KEY = "l#8Zz~1Ht!3Eb^7U";
    const AES_MODE = MCRYPT_MODE_ECB;
    const AES_CIPHER = 'AES-128-CBC';

    public static function ins($app = 'tg'){
        if(empty(self::$ins)){
            self::$ins = new self($app);
        }
        return self::$ins;
    }

    public function __construct($app = 'tg'){
        $this->logger = XLogKit::logger("_svc");
        $this->app = $app;
        if(!empty($_SERVER['LEVELEXPIRE']))  $this->levelExpire = intval($_SERVER['LEVELEXPIRE']);
        if(!empty($_SERVER['CODEEXPIRE'])) $this->codeExpire = intval($_SERVER['CODEEXPIRE']);
    }

    private function checkLevelLimit(){
        $userip = UFun::get_client_ip(1); //返回ip4数字
        $key = "level|{$this->app}|{$userip}";

        $view = CacheSvc::ins()->increment($key, 1);
        if(empty($view)){
            $view = CacheSvc::ins()->set($key, 1, 0, $this->levelExpire);
        }
        // CacheSvc::ins()->delete($key); //调试时候可关闭

        $this->upLeveLimit($view);
    }

    //按访问次数分级
    private function upLeveLimit($view = 1){
        $this->codeLen = 4;
        $this->codeType = self::CHAR_NUMBER;

        if($view <= 10){
            $this->level = 1;
        }else if($view > 10 && $view <= 20){
            $this->level = 2;
            $this->codeLen = 4; //6;
            $this->codeType = self::CHAR_NUMBER;
        }else if($view > 20 && $view <= 50){
            $this->level = 3;
            $this->codeLen = 4;
            $this->codeType = self::CHAR_LETTER;
        }else if($view > 50 && $view <= 300){
            $this->level = 4;
            $this->codeLen = 6;
            $this->codeType = self::CHAR_LETTER;
        }else{
            $this->level = self::DISABLE_LEVEL; //禁用
            $this->codeLen = 6;
            $this->codeType = self::CHAR_MIXTURE;
            $this->logger->warn("captcha disbaled", __CLASS__.'/'.__FUNCTION__);
        }
        $this->logger->debug("captcha Level:{$this->level} View:{$view}", __CLASS__.'/'.__FUNCTION__);
    }

    private function genCode(){
        $len = empty($this->codeLen) ? 4 : $this->codeLen;
        $letter = array('a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','u','v','w','x','y','z',
            'A','B','C','D','E','F','G','H','J','K','M','N','P','Q','R','S','U','V','W','X','Y','Z');
        $number = array('2','3','4','5','6','7','8','9');

        $chars = array();
        switch($this->codeType){
        case self::CHAR_NUMBER:
            $chars = $number;
            break;
        case self::CHAR_LETTER:
            $chars = $letter;
            break;
        case self::CHAR_MIXTURE:
            $chars = array_merge($letter, $number);
            break;
        default:
            $chars = $number;
            break;
        }

        //循环随机char
        $code = '';
        for($i=0; $i<$len; $i++){
            $code .= $chars[array_rand($chars)];
        }

        return $code;
    }

    public function create($width = 100, $height = 30, $isBorder = true){
        $this->checkLevelLimit(); //检查访问频度调整验证码难度
        $code = $this->genCode();
        $userip = UFun::get_client_ip(1); //返回ip4数字

        $data = array($this->app, $code, $userip, time());
        $str = implode(self::SP, $data);
        $tc_code = $this->encode($str);

        //设置加密串到Cookie
        $domain = implode('.', array_slice(explode('.', $_SERVER['SERVER_NAME']), -2, 2));
        header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        setcookie('tc_code', $tc_code, 0, '/', $domain);

        //创建并输出图片
        CImageSvc::ins($this->level)->createImage($code, $width, $height, $isBorder);
    }

    //校验用户提交验证码
    //$param $code 用户提交字符
    //$param $tc_code cookie加密字符
    public function verify($code = 0, $ip = 0, $tc_code = '', $codeExpire = 0){
        if($codeExpire > 0) $this->codeExpire = $codeExpire;

        $ret = array('errno'=>0, 'errmsg'=>'ok');
        $time = time();

        try{
            if(empty($code) || empty($tc_code)){
                throw new Exception('验证码无效');
            }
            $data = explode(self::SP, $this->decode($tc_code));
            if(count($data) != 4) throw new Exception('验证码数据不完整');

            list($app_crypt, $code_crypt, $ip_crypt, $time_crypt) = $data;
            $checkedKey = "checked|".md5($tc_code);

            //判断有效期
            if(($time - $time_crypt) > $this->codeExpire) throw new Exception('验证码已过期');

            //判断获取和校验客户端ip
            if($ip != $ip_crypt) throw new Exception('校验IP不对应');

            //判断字符串是否相同
            if(0 != strcmp(strToUpper($code), strToUpper($code_crypt))) throw new Exception('验证码错误');

            //判断是否已经验证过
            $checked = CacheSvc::ins()->get($checkedKey);
            if($checked >= 1) throw new Exception('验证码已被使用');

            //写入验证码校验记录
            CacheSvc::ins()->set($checkedKey, 1, 0, $this->codeExpire);

            $ret['errmsg'] = '验证码正确';
        }catch(Exception $e){
            $ret['errno'] = ($e->getCode() == 0) ? 1 : $e->getCode();
            $ret['errmsg'] = $e->getMessage();
        }
        return $ret;
    }

    public function encode($val = ""){
        // $val = str_pad($val, (16 * (floor(strlen($val) / 16) + 1)), chr(16 - (strlen($val) % 16)));
        // $vi = mcrypt_create_iv(mcrypt_get_iv_size(self::AES_CIPHER, self::AES_MODE),MCRYPT_RAND);
        // return bin2hex(mcrypt_encrypt(self::AES_CIPHER, self::AES_KEY, $val, self::AES_MODE, $vi));
        return openssl_encrypt($val, self::AES_CIPHER, self::AES_KEY, 0, self::AES_KEY);
    }

    public function decode($val = ""){
        // $vi = mcrypt_create_iv(mcrypt_get_iv_size(self::AES_CIPHER, self::AES_MODE),MCRYPT_RAND);
        // return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', mcrypt_decrypt(self::AES_CIPHER, self::AES_KEY, hex2bin($val), self::AES_MODE, $vi));
        return openssl_decrypt($val, self::AES_CIPHER, self::AES_KEY, 0, self::AES_KEY);
    }
}
