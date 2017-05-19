<?php
/**
 * Mysql数据操作
 */


class Db_Mysql extends Driver{

	/**
     * 数据库连接方法
     * @access public
     */

    public function connect(){

    }

    /**
     * 释放查询结果
     * @access public
     */

    public function free(){

    }

    /**
     * 启动事务
     * @access public
     * @return void
     */

    public function startTrans(){

    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */

    public function rollback(){

    }


    /**
     * 关闭数据库
     * @access public
     */

    public function close() {
        $this->_linkID = null;
    }

    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */

    public function escapeString($str) {
        return addslashes($str);
    }

    /**
     * 析构方法
     * @access public
     */

    public function __destruct(){

    }

}
