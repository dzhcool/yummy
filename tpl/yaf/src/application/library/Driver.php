<?php
/**
 * 数据库中间插件
 */

abstract class Driver{
    // PDO操作实例
    protected $PDOStatement = null;
    // 当前操作所属的模型名
    protected $model      = '';
    // 当前SQL指令
    protected $queryStr   = '';

    // 最后插入ID
    protected $lastInsID  = null;
    // 返回或者影响记录数
    protected $numRows    = 0;
    // 事务指令数
    protected $transTimes = 0;
    // 错误信息
    protected $error      = '';
    // 数据库连接ID 支持多个连接
    protected $linkID     = array();
    // 当前连接ID
    protected $_linkID    = null;
    // 数据库配置
    protected $config	  = array();

    protected $bind		  = array();


    /**
     * 数据库连接方法
     * @access public
     */

    abstract public function connect();

    /**
     * 释放查询结果
     * @access public
     */

    abstract public function free();

    /**
     * 启动事务
     * @access public
     * @return void
     */

    abstract public function startTrans();

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */

    abstract public function rollback();


    /**
     * 析构方法
     * @access public
     */

    abstract public function __destruct();

    /**
     * 关闭数据库
     * @access public
     */

    public function close() {
        $this->_linkID = null;
    }

    /**
     * 安全过滤
     * @access public
     * @param  array  $bind      参数
     */

    protected function _quote($value)
    {
        if (is_int($value)) {
            return $value;
        } elseif (is_float($value)) {
            return sprintf('%F', $value);
        }
        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }

}
