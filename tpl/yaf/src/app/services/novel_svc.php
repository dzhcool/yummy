<?php
/**
 * novel存储
 */
class NovelSvc
{
    private static $ins = null;

    public static function ins(){
        if(empty(self::$ins)){
            self::$ins = new self;
        }
        return self::$ins;
    }

    public function __construct(){
        $this->logger = XLogKit::logger("_svc");
    }

    public function save($chapterInfo, $content){
        if(empty($chapterInfo['name'])){
            $short = UFun::short($chapterInfo['id'].'_'.$chapterInfo['volume_id'].'_'.$chapterInfo['lnovel_id']).'.txt';
            $name = 'tmp/novel/'.$chapterInfo['lnovel_id'].'/'.$chapterInfo['volume_id'].'/'.$chapterInfo['id'].'_'.$short;
        }
        $res = $this->tofile($name, $content);
        if(!$res){
            return "";
        }
        return $name;
    }

    private function tofile($filename, $content){
        $file = $_SERVER['PRJ_ROOT'].'/'.$filename;
        $tmpfile = $file.'.tmp';

        UFun::mkdirs(dirname($file));

        $handle = fopen($tmpfile, 'w+');
        if(!$handle){
            return false;
        }

        $rs = fwrite($handle, $content);
        fclose($handle);
        if($rs <= 0){
            return false;
        }

        if(file_exists($file)){
            unlink($file);
        }

        $rs = rename($tmpfile, $file);
        if(!$rs){
            return false;
        }
        return true;
    }

}
