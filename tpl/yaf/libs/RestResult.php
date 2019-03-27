<?php

class RestResult
{
    const BIZ_UNKNOW     = 1100;
    private $status_code = 500 ;
    private $errno       = 1 ;
    private $errmsg      = "unknown";
    private $data        = array();
    private $type        = 'json';
    private $value       = '';
    static $_ins         = null;

    static public function ins()
    {
        if (self::$_ins == null)
        {
            self::$_ins = new self();
        }
        return self::$_ins;
    }

    public function getData()
    {
        return  $this->data;

    }
    /**
     ** @brief  设置错误
     **
     ** @param $errMsg
     ** @param $errNo
     ** @param $status_code
     **
     ** @return
     **/
    public function error($errmsg,$errno = self::BIZ_UNKNOW,$status_code = 200)
    {
        $this->errno            = $errno ;
        $this->errmsg           = $errmsg ;
        $this->status_code      = $status_code ;
        return $this;
    }
    /**
     ** @brief 设置成功
     **
     ** @param $data  需要是数组
     ** @param $status_code
     **
     ** @return
     **/
    public function success($data = "",$status_code = 200)
    {
        $this->errno        = 0  ;
        $this->errmsg       = "" ;
        $this->status_code    = $status_code ;
        $this->data         = $data ;
        return $this;
    }
    public function is_success()
    {
        return  $this->errno == 0 ;
    }
    public function setType($type='json', $value='')
    {
        $this->type = $type;
        $this->value = $value;
        return $this;
    }
    public function show()
    {
        $datas['errno']     = $this->errno ;
        $datas['errmsg']    = $this->errmsg ;
        $datas['data']      = $this->data;
        $json_data          = json_encode($datas);

        if ('raw' == $this->type) {
            echo $this->data;
        } else if ('jsonp' == $this->type) {
            echo $this->value . '(' . $json_data . ')';
        } else {
            echo $json_data;
        }
        return true;
    }
}
