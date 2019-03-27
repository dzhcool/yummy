<?php
// 默认上传文件至七牛云存储
class LUpload
{
    static public function upload($filePath, $name = null, $bucket = null)
    {
        $ins = QiniuUpload::ins($bucket);
        try {
            return $ins->upload($filePath, $name);
        } catch (Exception $e) {
            $ins->logger->error(__CLASS__."::upload fail filePath=".$filePath.", [errMsg]:".$e->getMessage());
            throw new Err_Res($e->getMessage());
        }
    }

    static public function multiUpload($files, $bucket=null)
    {
        $ins = QiniuUpload::ins($bucket);
        try {
            return $ins->multiUpload($files);
        } catch (Exception $e) {
            $ins->logger->error(__CLASS__."::multiUpload fail files=".json_encode($files).", [errMsg]:".$e->getMessage());
            throw new Err_Res($e->getMessage());
        }
    }

    static public function delete($name = null, $bucket = null)
    {
        $ins = QiniuUpload::ins($bucket);
        try {
            return $ins->delete($name);
        } catch (Exception $e) {
            $ins->logger->error(__CLASS__."::delete fail name=".$name.", [errMsg]:".$e->getMessage());
            throw new Err_Res($e->getMessage());
        }
    }

    static public function multiDelete($files, $bucket=null)
    {
        $ins = QiniuUpload::ins($bucket);
        try {
            return $ins->multiDelete($files);
        } catch (Exception $e) {
            $ins->logger->error(__CLASS__."::multiDelete fail files=".json_encode($files).", [errMsg]:".$e->getMessage());
            throw new Err_Res($e->getMessage());
        }
    }

}

use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;

class QiniuUpload implements IUpload
{
    private static $_ins = null;

    private function __construct($bucket='')
    {
        $this->bucket = $bucket ? $bucket : $_SERVER['QINIU_BUCKET'];
        $this->logger = XLogKit::logger("_upload");
        $auth        = new Auth($_SERVER['QINIU_AK'], $_SERVER['QINIU_SK']);
        $this->token = $auth->uploadToken($this->bucket);
        $this->bucketMgr = new BucketManager($auth);
        $this->uploadMgr = new UploadManager();
    }

    public static function ins($bucket)
    {
        if (!isset(self::$_ins)) {
            self::$_ins = new QiniuUpload($bucket);
        }
        return self::$_ins;
    }

    /**
        * @brief 上传单个文件
        *
        * @param $filePath 源文件路径
        * @param $name 目标文件名
        *
        * @return
     */
    public function upload($filePath, $name = null)
    {
        list($ret, $err) = $this->uploadMgr->putFile($this->token, $name, $filePath);
        if ($err !== null)
        {
            throw new Err_Res($err->message());
        }
        return $ret['key'] ? $ret['key'] : false;
    }

    /**
        * @brief 批量上传文件
        *
        * @param $files array(
        *   array('filepath'=>'xxx', 'name'=>'xxx.jpg'),
        *   array('filepath'=>'xxx', 'name'=>'xxx.jpg'),
        * )
        *
        * @return
     */
    public function multiUpload(array $files)
    {
        if (!is_array($files))
        {
            throw new Err_Input('批量上传文件，files必须为数组格式');
        }
        if (empty($files)) return FALSE;

        $ret = array();
        foreach($files as $file)
        {
            $ret[] = $this->upload($file['filePath'], $file['name']);
        }
        return $ret;
    }

    /**
        * @brief 删除文件
        *
        * @param $name
        *
        * @return
     */
    public function delete($name)
    {
        $err = $this->bucketMgr->delete($this->bucket, $name);
        if ($err !== null)
        {
            throw new Err_Res($err->message());
        }
        return TRUE;
    }

    /**
        * @brief 批量删除文件
        *
        * @param $files array('xxx','xxx')
        *
        * @return
     */
    public function multiDelete(array $files)
    {
        if (!is_array($files))
        {
            throw new Err_Input('批量删除文件，files必须为数组格式');
        }
        if (empty($files)) return FALSE;

        $ret = array();
        foreach($files as $name)
        {
            $ret[] = $this->delete($name);
        }
        return $ret;
    }

}

interface IUpload
{
    public function upload($filepath);
    public function delete($name);
}
