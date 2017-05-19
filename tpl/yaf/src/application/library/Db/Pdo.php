<?php
/**
 * PDO数据库操作
 */


class Db_Pdo extends Driver{

    //PDO的参数
    private $options = array(
        PDO::ATTR_CASE              =>  PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE           =>  PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      =>  PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES =>  false,
    );
    // 完整sql语句
    private $sql = '';
    // 语句拼装
    private $parts = array();
    //解析数组
    private $_partsInit = array('distinct', 'field', 'union', 'table', 'where', 'group', 'having', 'order', 'limit', 'offset', 'lock');
    // 要查询表名称
    public  $truetable;

    public function __construct($config){
        //检测配置文件
        $this->_checkRequiredOptions($config);
        $this->config = $config;
    }

    /**
     * 数据库连接方法
     * @access public
     */

    public function connect($linkNum=0){
        if(!isset($this->linkID[$linkNum])){
            try{
                //当PHP5.3.6时候禁止模拟预处理
                if(version_compare(PHP_VERSION,'5.3.6','<=')){
                    $this->options[PDO::ATTR_EMULATE_PREPARES]  =   false;
                }
                //判断DSN是否存在
                if(empty($this->config['dsn'])) $this->config['dsn'] = parseDsn();
                //实例化PDO
                $this->linkID[$linkNum] = new PDO( $this->config['dsn'], $this->config['username'], $this->config['password'], $this->options);
            }catch(PDOException $e){
                Error($e->getMessage());
            }
        }
        return $this->linkID[$linkNum];
    }

    /**
     * 监测配置文件
     * @access public
     */

    private function _checkRequiredOptions($config){
        if( !array_key_exists("database", $config) ){
            Error(Lang('_NO_DB_DBNAME_'));
        }
        if( !array_key_exists("username", $config) ){
            Error(Lang('_NO_DB_NOUSER_'));
        }
        if( !array_key_exists("password", $config) ){
            Error(Lang('_NO_DB_PASSWD_'));
        }
    }

    /**
     * 数据库初始化方法
     * @access public
     */

    public function init($master=true){
        //主从分离多服务器连接暂时不做
        //判断数据库资源是否存在，不存在连接
        if ( !$this->_linkID ) $this->_linkID = $this->connect();
    }

    /**
     * 数据库初始化方法
     * @access public
     */

    public function parseDsn(){
        $dsn  =   'mysql:dbname='.$this->config['database'].';host='.$this->config['hostname'];
        if(!empty($this->config['hostport'])) {
            $dsn  .= ';port='.$this->config['hostport'];
        }elseif(!empty($this->config['socket'])){
            $dsn  .= ';unix_socket='.$this->config['socket'];
        }
        if(!empty($this->config['charset'])){
            // PHP5.3.6以下不支持charset设置
            if(version_compare(PHP_VERSION,'5.3.6','<')){
                $this->options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES '.$this->config['charset'];
            }else{
                $dsn  .= ';charset='.$this->config['charset'];
            }
        }
        return $dsn;
    }

    /**
     * 查询数据表信息
     * @access public
     */

    public function getFields($tableName){
        $sql    = 'SHOW COLUMNS FROM `'.$tableName.'`';
        $this->query($sql);
        $result = $this->getAll();
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$val['field']] = array(
                'name'    => $val['field'],
                'type'    => $val['type'],
                'notnull' => (bool) ($val['null'] === ''),
                'default' => $val['default'],
                'primary' => (strtolower($val['key']) == 'pri'),
                'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
            );
        }
        return $info;
    }

    /**
     * 插入数据
     * @param  array $data 数据关联数组
     * @access public
     */

    public function insert($data){
        $sql = "INSERT INTO `$this->truetable` (`".join(array_keys($data),'`,`')."`) VALUES (:".join(array_keys($data),',:').")";
        $this->query($sql,$data);
        return $this->_linkID->lastInsertId();
    }

    /**
     * 更新数据表
     * @param  array $info 数据关联数组
     * @access public
     */

    public function update($info){
        $args = array();
        foreach ($info as $k => $v) {
            $args [] = "`$k`=:$k";
        }
        $where = $this->parts['where'];
        if(empty($where)) new Yaf\Exception("not exists where method");
        $sql = "UPDATE `$this->truetable` SET ".join($args,',').$where;
        $this->query($sql,$info);
        return $this->PDOStatement->rowCount();
    }

    /**
     * 删除数据
     * @param  数据关联数组
     * @access public
     */

    public function delete(){
        $this->getTable();
        $where =  $this->parts['where'];
        if(empty($where)) new Yaf\Exception("not exists where method");
        $sql = "DELETE ".$this->parts['table']. $this->parts['where'];
        $this->query($sql);
        return $this->PDOStatement->rowCount();
    }

    /**
     * 查找记录
     * @access public
     * @return 结果集
     */

    public function select() {
        $sql = $this->selectSql();
        $this->query($sql);
        return $this->getAll();
    }

    /**
     * 解析where参数
     * @access public
     * @param  array  $where      参数
     */

    public function where($where) {
        // 判断where条件是否为空
        if(empty($where)){
            return $where;
        }
        // 如果不是数组转换成数组
        if(!is_array($where)){
            $where = array($where);
        }
        // 处理where语句
        foreach ($where as $key => &$val) {
            if(is_numeric($key)){
                $val = (string)$val;
            }else{
                $val = $this->_where($key, $val);
            }
            $val = '(' . $val . ')';
        }
        // 多条件合并where语句
        $where = implode(' AND ', $where);
        $this->parts['where'] = ' WHERE '.$where;
    }

    /**
     * 解析where特殊参数
     * @access public
     * @param  string        $key
     * @param  string|array  $val
     */

    private function _where($key,$val){
        if(!is_array($val)){
            return "$key  = ".$this->_quote($val);
        }else{

            switch (strtolower($val[0])) {
            case 'eq':
                return "$key  = ".$this->_quote($val[1]);
                break;
            case 'neq':
                return "$key  != ".$this->_quote($val[1]);
                break;
            case 'gt':
                return "$key  > ".$this->_quote($val[1]);
                break;
            case 'egt':
                return "$key  >= ".$this->_quote($val[1]);
                break;
            case 'lt':
                return "$key  < ".$this->_quote($val[1]);
                break;
            case 'elt':
                return "$key  <= ".$this->_quote($val[1]);
                break;
            case 'in':
                return "$key IN (".$this->_quote($val[1]).")";
                break;
            case 'not in':
                return "$key NOT IN (".$this->_quote($val[1]).")";
                break;
            case 'in':
                return "$key IN (".$this->_quote($val[1]).")";
                break;
            }
        }
    }

    /**
     * 解析distinct参数
     * @access public
     */

    public function distinct(){
        $this->parts['distinct'] = ' distinct ';
    }

    /**
     * 解析field参数
     * @access public
     * @param  array|string  $fields  字段参数
     */

    public function field($fields){
        if(is_string($fields)){
            $this->parts['field'] =' '.(string)$fields.' ';
        }
        if(is_array($fields)){
            $this->parts['field'] = ' '.implode($fields, ',').' ';
        }
    }

    /**
     * 解析order方法
     * @access public
     * @param  string  $order  排序字符串
     */

    public function order($order){
        $val = '';
        if (preg_match('/(.*\W)(ASC|DESC)\b/si', $order, $matches)) {
            $val = trim($matches[1]);
            $direction = $matches[2];
        }else{
            $direction = 'ASC';
        }
        if(empty($val)) $val = $order;
        $this->parts['order'] = " ORDER BY $val $direction";
    }

    /**
     * 解析limit方法
     * @access public
     * @param  int      $count
     * @param  int      $offset
     */

    public function limit($count,$offset=0) {
        $count = intval($count);
        if ($count <= 0) {
            Error("limit argument  is not valid");
        }
        $offset = intval($offset);

        if( $offset < 0 ){
            Error("offset argument  is not valid");
        }

        $this->parts['limit'] = " Limit $count";

        if( $offset>0 ){
            $this->parts['offset'] = " OFFSET $offset";
        }

    }

    /**
     * 获取table表
     * @access public
     */

    public function getTable(){
        if(empty($this->truetable)) Error();
        $this->parts['table'] = " FROM $this->truetable ";
    }

    /**
     * 解析select语句
     * @access public
     */

    private function selectSql(){
        $this->getTable();
        if(empty($this->parts)){
            return false;
        }
        $SQL = "SELECT ";
        //字段为空
        if(!isset($this->parts['field'])) $SQL .= " * ";
        foreach ($this->_partsInit as $parts) {
            if(isset($this->parts[$parts])){
                $SQL .= $this->parts[$parts];
            }
        }
        return $SQL;
    }


    /**
     * 执行查询
     * @access public
     * @param  string    $sql  sql指令
     * @param  boolean   $bind 执行参数
     */

    public function query($sql,$bind=array()){
        //初始化连接
        $this->init();
        //如果连接失败
        if(!$this->_linkID) return false;
        //记录sql指令
        $this->queryStr = $sql;
        //释放前次的查询结果
        if ( !empty($this->PDOStatement) ) $this->free();
        //PDO预处理
        $this->PDOStatement = $this->_linkID->prepare($sql);
        //参数绑定
        if(!empty($bind)) $this->bindSql($bind,$sql);
        //执行预处理语句
        $this->PDOStatement->execute();
    }

    /**
     * 获取结果集
     * @access public
     * @return array   结果集
     */

    public function getAll(){
        if($this->PDOStatement == NULL){
            return false;
        }
        $result = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        $this->numRows = count( $result );
        return $result;
    }

    /**
     * 解析参数绑定
     * @access public
     * @param  array  $bind      参数
     */

    public function bindSql($bind = array()){
        foreach($bind as $key =>$val){
            if(is_numeric($key)){
                $this->PDOStatement->bindValue($key+1, $val);
            }else{
                $this->PDOStatement->bindValue($key, $val);
            }
        }
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */

    public function error(){
        //捕获错误信息
        if($this->PDOStatement){
            $errorInfo = $this->PDOStatement->errorInfo();
            $this->error = $errorInfo[1].':'.$errorInfo[2];
            $errorCode = $this->PDOStatement->errorCode();
        }else{
            $errorInfo = '';
            $errorCode = 1000;
        }
        //如果sql语句不为空
        if(!empty($this->queryStr)){
            $this->error .= "\n[SQL语句]：".$this->queryStr;
        }
        Error($this->error,$errorCode);
    }

    /**
     * 释放查询结果
     * @access public
     */

    public function free(){
        $this->PDOStatement = null;
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
     * 析构方法
     * @access public
     */

    public function __destruct(){
        //释放资源
        if($this->_linkID){
            $this->_linkID = null;
        }
        //释放结果集
        $this->PDOStatement = null;
    }

}
