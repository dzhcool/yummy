<?php
/**
 * 验证码绘制
 * @author dangzihao
 * @date 2016-01-10
 */
class CImageSvc
{
    private static $ins = null;

    //图片
    private $im = null;

    //缺省参数
    private $type = 'png';
    private $width = 130;
    private $height = 30;
    private $level = 1;
    private $color = array();
    private $minFontSize = 23;
    private $maxFontSize = 25;
    private $fontWidth = 16; //文字间距，类似于文字宽度  12
    private $circleNum = 12; //干扰点数量
    private $isBorder = false; //是否显示边框

    //leve禁用级别
    const DISABLE_LEVEL = 9999;

    //配置
    private $fonts = array("BASKVILL.ttf", "comic.ttf", "LFAX.ttf", "lucon.ttf", "zpix.ttf");
    private $colors = array(
        array("r"=>66,"g"=>133, "b"=>244),
        array("r"=>234,"g"=>67, "b"=>53),
        array("r"=>52,"g"=>170, "b"=>84),
        array("r"=>68,"g"=>170, "b"=>236),
        array("r"=>4,"g"=>98, "b"=>180),
        array("r"=>220,"g"=>46, "b"=>76),
        array("r"=>124,"g"=>146, "b"=>116),
    );
    private $angles = array(0, 5, 10, 15); //array(0, 10, 15, 25);

    public static function ins($level = 1){
        if(empty(self::$ins)){
            self::$ins = new self($level);
        }
        return self::$ins;
    }

    public function __construct($level = 1){
        $this->level = $level;

        //随机颜色
        $this->color = $this->colors[mt_rand(0, count($this->colors)-1)];
    }

    public function createImage($words, $width = 0, $height = 0, $isBorder = true){
        if(!empty($width)) $this->width = $width;
        if(!empty($height)) $this->height = $height;
        $this->isBorder = $isBorder;


        try{
            if($this->level >= self::DISABLE_LEVEL) throw new Exception('disabled');
            $this->drawBase(); //绘制基础背景
            $this->drawWords($words); //绘制文字
            if($this->level > 0){
                $this->drawStrikeThrough(); //绘制干扰线
                $this->drawCircles(); //绘制干扰点
            }
        }catch(Exception $e){
            //禁用验证码生成
            $this->drawDisabled();
        }

        $this->displayImage(); //输出图片
    }

    private function drawDisabled(){
        $disabled_gif  = $_SERVER['PRJ_ROOT'].'/src/web_inf/admin/static/img/disabled.gif';
        $this->im = @imagecreatefromgif($disabled_gif);

        //绘制边框
        if($this->isBorder == true){
            $black = imagecolorallocate($this->im, 0 , 0, 0);
            imagerectangle($this->im, 0, 0, $this->width - 1, $this->height - 1, $black);
        }
    }

    private function drawBase(){
        $this->im = imagecreate($this->width, $this->height); //创建图片
        imagecolorallocate($this->im, 255 , 255, 255);

        //绘制边框
        if($this->isBorder == true){
            $black = imagecolorallocate($this->im, 0 , 0, 0);
            imagerectangle($this->im, 0, 0, $this->width - 1, $this->height - 1, $black);
        }
    }

    private function drawWords($words){
        $len = strlen($words); //验证码字符长度

        $textColor = imagecolorallocate($this->im, $this->color['r'] , $this->color['g'], $this->color['b']);

        $lastX = 0; //上一次字符X

        for($i=0; $i<$len; $i++){
            $text   = $words[$i];
            $angle_limit = $this->angles[$this->level];
            $angle  = mt_rand(-$angle_limit, $angle_limit);
            $fontsize   = mt_rand($this->minFontSize, $this->maxFontSize);

            if($lastX == 0){
                $x = $this->width / $len * 0.45;
            }else{
                if(preg_match('/([\d]+)$/', $text)){
                    $xZone = $fontsize / 2; //数组紧凑点
                }else{
                    $xZone = $fontsize / 1.5;
                }
                $x = $lastX + $xZone + mt_rand(-$this->level/3, max(1, $angle_limit/4));
            }
            $yZone = $this->height / 9;
            $y = $this->height * 0.75 + rand($yZone, $yZone*2);

            $font   = $_SERVER['PRJ_ROOT'].'/libs/Captcha/fonts/'.$this->fonts[mt_rand(0, count($this->fonts)-1)];
            imagettftext($this->im, $fontsize, $angle, $x, $y, $textColor, $font, $text);

            $lastX = $x;
        }
    }

    private function drawStrikeThrough(){
        $color = imagecolorallocatealpha($this->im, $this->color['r'], $this->color['g'], $this->color['b'], mt_rand(20, 25));
        $maxx = $this->width - 1;
        $maxy = $this->height - 1;
        $y = mt_rand($maxy/3, $maxy-$maxy/3);

        $amplitude = mt_rand(5, 15);
        $period = mt_rand(80, 180);
        $dx = 2.0 * M_PI / $period;
        for($x=0; $x<$maxx; $x=$x+0.9){
            $xo = $amplitude * cos(floatval($y) * $dx);
            $yo = $amplitude * sin(floatval($x) * $dx);

            for($yn=0; $yn<$maxy; $yn++){
                $r = mt_rand(0, $maxy);
                imagefilledellipse ($this->im, round($x+$xo), round($y+$yo+$yn*$maxy), 2, 2, $color);
            }
        }
    }

    private function drawCircles(){
        $color = imagecolorallocatealpha($this->im, $this->color['r'], $this->color['g'], $this->color['b'], mt_rand(20, 80));
        for($i=0; $i<$this->circleNum; $i++){
            $size = mt_rand(3, 6);
            $x = mt_rand($size, $this->width-$size);
            $y = mt_rand($size, $this->height-$size);
            imagefilledellipse ($this->im, $x, $y, $size, $size, $color);
        }
    }

    private function displayImage(){
        switch($this->type){
            case "png":
                @header("Content-type: image/png");
                imagepng($this->im);
                break;
            default:
                @header("Content-type: image/gif");
                imagegif($this->im);
                break;
        }
        imagedestroy($this->im);
    }
}
