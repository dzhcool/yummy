<?php
/**
* @brief Mysql实体
* 使用方法
* @code
* class UserModel extends LunaEntity
* {
*   protected $_table = 'user'; //可不设置，默认为类名
*   protected $_pkey  = 'id';  //可不设置，默认为id
* }
* @endcode
 */
class LunaEntity extends Entity
{

    const STATUS_ON  = 1; //开启|正常状态
    const STATUS_OFF = 2; //关闭|禁用状态
    const STATUS_DEL = 3; //记录删除

    //查询一列数据
    public static function getField($conds=array(), $fields)
    {
        $d = self::ins()->multiGet($conds, array($fields));
        return array_column($d,$fields);
    }
    // 通过主键获取单条记录
    public static function get($id, $fields = array())
    {
        $id  = self::checkPkey($id, '查询主键输入错误', 1);
        return self::ins()->getOne(array(self::ins()->_pkey => $id), $fields);
    }

    // 查询单条记录
    public static function findOne($conds, $fields = array())
    {
        return self::ins()->getOne($conds, $fields);
    }

    // 判断主键ID的记录是否存在
    public static function exist($id)
    {
        $obj = self::ins()->getOne(array(self::ins()->_pkey => $id));
        return $obj ? true : false;
    }

    // 通过主键删除记录，默认逻辑删除即修改记录状态为STATUS_DEL
    public static function delete($id)
    {
        $id = self::checkPkey($id, '删除失败:ID输入错误', 1);
        $obj = self::get($id);
        if (!$obj)
        {
            throw new Err_Input('删除失败:ID不存在');
        }
        return self::ins()->e_del(array(self::ins()->_pkey => $id));
    }

    // 通过主键更新记录
    public static function modify($id, $data = array(), $skip = false)
    {
        $pkey = self::ins()->_pkey;
        if (isset($data[$pkey]))
        {
            unset($data[$pkey]);
        }

        // skip the same vals
        if ($skip)
        {
            $info = self::get($id);
            foreach($data as $key => $val)
            {
                if ($val == $info[$key])
                {
                    unset($data[$key]);
                }
            }
        }
        if (empty($data)) return true;
        return self::ins()->e_update($data, array($pkey => $id) );
    }

    // 保存记录，没有则新增，有则更新
    public static function save($data = array())
    {
        $pkey = self::ins()->_pkey;
        if (!self::exist($data[$pkey])) {
            $id = self::insert($data);
            return $id ? self::get($id) : false;
        }
        $rst = self::modify($data[$pkey], $data);
        return $rst ? self::get($data[$pkey]) : false;
    }

    // 新增记录
    public static function insert($data = array())
    {
        $pkey = self::ins()->_pkey;
        if (empty($data[$pkey]))
        {
            unset($data[$pkey]);
        }
        if (empty($data)) return false;
        $obj = self::ins();
        foreach($data as $col=>$val)
        {
            $obj->{$col} = $val;
        }
        return $obj->create();
    }

    // 所有所有记录
    public static function getall($conds = array(), $fields = array(), $sort = null)
    {
        return self::ins()->multiGet($conds, $fields, $sort);
    }

    // 通过条件获取记录列表
    public static function gets($conds = array(), $fields = array(), $sort = null, $limit= 20, $skip = 0)
    {
        return self::ins()->multiGet($conds, $fields, $sort, $limit, $skip);
    }

    // 通过条件获取总数和列表
    public static function getsbypage($conds = array(), $fields = array(), $sort = null, $page = 1, $size= 20)
    {
        $total = self::count($conds);
        $pageconfig = array(
            'total'     => $total,
            'curpage'   => $page,
            'totalpage' => ceil($total/$size),
        );
        $rst = array('pageconfig'=>$pageconfig, 'list'=>array());
        if (!empty($rst['pageconfig']['total']))
        {
            $rst['list'] = self::ins()->multiGet($conds, $fields, $sort, $size, ($page-1)*$size);
        }
        return $rst;
    }

    // 统计记录数量
    public static function count($conds = array())
    {
        return self::ins()->getCount($conds);
    }

    // 手动执行sql
    public static function query($sql)
    {
        return self::ins()->exec($sql);
    }

    // 开启事务
    public static function begin()
    {
        return self::ins()->e_begin();
    }

    // 是否在事务中
    public static function isbegin()
    {
        return self::ins()->e_isbegin();
    }

    // 提交事务
    public static function commit()
    {
        return self::ins()->e_commit();
    }

    // 回滚事务
    public static function rollback()
    {
        return self::ins()->e_rollback();
    }

    // 逐条获取记录,$list = getforeach(); foreach($list as $obj)
    public static function getforeach($conds = array(), $fields = array())
    {
        return self::ins()->yieldAll($conds, $fields);
    }

    // 获取pdo连接
    public static function pdo()
    {
        return self::ins()->getPdo();
    }

    private static function isDefaultKey()
    {
        return self::ins()->_pkey == 'id';
    }

    // 检查主键是否为空
    private static function checkPkey($id, $msg, $min = 1)
    {
        return (self::isDefaultKey() || is_int($id)) ? LCheck::int($id, $msg, $min) : LCheck::string($id, $msg, $min);
    }

    // yaf register mysql executer
    public static function register()
    {
        $pdo = Yaf_Registry::get(Entity::SQL_EXEC);
        if ($pdo)
        {
            return $pdo;
        }

        $mysqlConf = Yaf_Registry::get('config')->mysql;
        if($mysqlConf) {
            $dsn = "mysql:host=" . $mysqlConf['host'] . ";port=" . $mysqlConf['port'] . ";dbname=" . $mysqlConf['dbname'];
            $pdo = new PDO( $dsn, $mysqlConf["user"], $mysqlConf["pwd"], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            #PDO::ERRMODE_SILENT 默认模式不报错
            #PDO::ERRMODE_WARNING 页面主动报错warning
            #PDO::ERRMODE_EXCEPTION 主动抛出异常，需要catch
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $executor  = new Aura\Sql\ExtendedPdo($pdo);
            Yaf_Registry::set(Entity::SQL_EXEC, $executor);
            return $executor;
        }
        throw new Err_Res('mysql config is empty');
    }
}

class Entity
{
    const SQL_EXEC = "mysql_executer";

    protected $pdo;
    protected $_table;
    protected $_pkey = 'id';
    protected $_attr = array();
    static private $_ins = null;

    public static function ins() {
        $class = get_called_class();
        if (empty(self::$_ins[$class])) {
            self::$_ins[$class] = new $class($class);
        }
        return self::$_ins[$class];
    }

    public function __construct($class)
    {
        $this->pdo    = LunaEntity::register();
        $this->_table = $this->_table ? $this->_table : strtolower(str_replace('Model', '', $class));
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function __set($k, $v)
    {
        $this->_attr[$k] = $v;
    }

    public function __get($k)
    {
        return $this->_attr[$k];
    }

    protected function create()
    {
        if (empty($this->_attr)) {
            return true;
        }
        $row = $this->_attr;
        $table = $this->_table;
        $cols = array_keys($row);
        $vals = array();

        foreach ($cols as $col) {
            $vals[] = ":$col";
        }

        $cols = implode(",", $cols);
        $vals = implode(",", $vals);
        $stm = "INSERT INTO {$table} ({$cols}) VALUES ({$vals})";

        $this->log($stm, $row);

        try {
            $this->pdo->perform($stm, $row);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
        return intval($this->pdo->lastInsertId());
    }

    protected function log($stm, $vals = array())
    {
        if ($vals)
        {
            foreach($vals as $col => $value)
            {
                if (!is_int($value))
                {
                    $value = "'" . $value . "'";
                }

                $stm = str_replace(":$col", $value, $stm);
            }
        }
        XLogKit::logger("_sql")->info(" Sql:" . $stm);
    }

    protected function getOne($conds, $fields = array())
    {
        if (!is_array($conds) or empty($conds)) {
            return array();
        }
        $table = $this->_table;
        $cols  = array_keys($conds);
        $vals  = array();

        foreach ($cols as $col) {
            if ($col == 'between') {
                foreach($conds['between'] as $k => $v) {
                    if (is_string($v[0])) {
                        $vals[] = "$k >= \"{$v[0]}\" AND $k <= \"{$v[1]}\"";
                    } else {
                        $vals[] = "$k >= {$v[0]} AND $k <= {$v[1]}";
                    }
                }
                unset($conds['between']);
                continue;
            }
            if ($col == 'or') {
                foreach ($conds['or'] as $k => $v) {
                    if (is_string($v)) {
                        $vals[] = "$k >= {$v[0]} OR $k <= {$v[1]}";
                    } else {
                        $vals[] = "$k = \"{$v[0]}\" OR $k = \"{$v[1]}\"";
                    }
                }
                unset($conds['or']);
                continue;
            }
            if($col == 'in'){
                foreach($conds['in'] as $k => $v) {
                    if (is_string($v)) {
                        $vals[] = "$k in \"{$v}\" ";
                    } else {
                        $vals[] = $k." in (".strval(implode(',',$v)).')';
                    }
                }
                unset($conds['in']);
                continue;
            }
            if($col == 'like'){
                foreach($conds['like'] as $k => $v) {
                    if (is_string($v)) {
                        $vals[] = "$k like \"{$v}\" ";
                    } else {
                        $vals[] = "$k like {$v}";
                    }
                }
                unset($conds['like']);
                continue;
            }
            $vals[] = "$col = :$col";
        }

        $vals = implode(" AND ", $vals);
        if ($fields) {
            $fields = implode(",", $fields);
        } else {
            $fields = "*";
        }
        $stm = "SELECT {$fields} FROM {$table} WHERE  $vals";
        $this->log($stm, $conds);

        try {
            return $this->pdo->fetchOne($stm, $conds);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function getCount($conds = array())
    {
        $table = $this->_table;
        $stm = "SELECT COUNT(1) as total FROM {$table}";

        if ($conds)
        {
            $cols  = array_keys($conds);
            $vals  = array();

            foreach ($cols as $col) {
                if ($col == 'between') {
                    foreach($conds['between'] as $k => $v) {
                        if (is_string($v[0])) {
                            $vals[] = "$k >= \"{$v[0]}\" AND $k <= \"{$v[1]}\"";
                        } else {
                            $vals[] = "$k >= {$v[0]} AND $k <= {$v[1]}";
                        }
                    }
                    unset($conds['between']);
                    continue;
                }
                if ($col == 'or') {
                    foreach ($conds['or'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k >= {$v[0]} OR $k <= {$v[1]}";
                        } else {
                            $vals[] = "$k = \"{$v[0]}\" OR $k = \"{$v[1]}\"";
                        }
                    }
                    unset($conds['or']);
                    continue;
                }
                if($col == 'in'){
                    foreach($conds['in'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k in \"{$v}\" ";
                        } else {
                            $vals[] = $k." in (".strval(implode(',',$v)).')';
                        }
                    }
                    unset($conds['in']);
                    continue;
                }
                if($col == 'like'){
                    foreach($conds['like'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k like \"{$v}\" ";
                        } else {
                            $vals[] = "$k like {$v}";
                        }
                    }
                    unset($conds['like']);
                    continue;
                }
                if($col == 'neq'){
                    foreach($conds['neq'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k <> \"{$v}\" ";
                        }
                    }
                    unset($conds['neq']);
                    continue;
                }
                if($col == '_string'){
                    $vals[] = $conds['_string'];
                    unset($conds['_strings']);
                    continue;
                }
                $vals[] = "$col = :$col";
            }

            $vals = implode(" AND ", $vals);
            $stm .= " WHERE  $vals";
        }
        $this->log($stm, $conds);
        try {
            $rst = $this->pdo->fetchOne($stm, $conds);
            return intval($rst['total']);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function multiGet($conds = array(), $fields = array(), $sort = null, $limit = null, $skip = null)
    {
        $table = $this->_table;

        if ($fields) {
            $fields = implode(",", $fields);
        } else {
            $fields = "*";
        }
        $stm = "SELECT {$fields} FROM {$table}";
        if ($conds) {
            $cols  = array_keys($conds);

            $vals  = array();
            foreach ($cols as $col) {
                if ($col == 'between') {
                    foreach($conds['between'] as $k => $v) {
                        if (is_string($v[0])) {
                            $vals[] = "$k >= \"{$v[0]}\" AND $k <= \"{$v[1]}\"";
                        } else {
                            $vals[] = "$k >= {$v[0]} AND $k <= {$v[1]}";
                        }
                    }
                    unset($conds['between']);
                    continue;
                }
                if ($col == 'or') {
                    foreach ($conds['or'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k >= {$v[0]} OR $k <= {$v[1]}";
                        } else {
                            $vals[] = "$k = \"{$v[0]}\" OR $k = \"{$v[1]}\"";
                        }
                    }
                    unset($conds['or']);
                    continue;
                }
                if($col == 'in'){
                    foreach($conds['in'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k in \"{$v}\" ";
                        } else {
                            $vals[] = $k." in (".strval(implode(',',$v)).')';
                        }
                    }
                    unset($conds['in']);
                    continue;
                }
                if($col == 'like'){
                    foreach($conds['like'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k like \"{$v}\" ";
                        } else {
                            $vals[] = "$k like {$v}";
                        }
                    }
                    unset($conds['like']);
                    continue;
                }
                if($col == 'neq'){
                    foreach($conds['neq'] as $k => $v) {
                        if (is_string($v)) {
                            $vals[] = "$k <> \"{$v}\" ";
                        }
                    }
                    unset($conds['neq']);
                    continue;
                }
                if($col == '_string'){
                    $vals[] = $conds['_string'];
                    unset($conds['_strings']);
                    continue;
                }
                $vals[] = "$col = :$col";
            }

            $vals = implode(" AND ", $vals);
            $stm .= " WHERE  $vals";
        }
        if ($sort)
        {
            $stm .= " ORDER BY $sort";
        }
        if ($limit || $skip)
        {
            $stm .= " LIMIT $skip,$limit";
        }
        $this->log($stm, $conds);
        try {
            return $this->pdo->fetchAll($stm, $conds);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function e_update($attr, $conds)
    {
        if (empty($attr) || empty($conds)) {
            return false;
        }
        $table = $this->_table;

        $cols  = array_keys($conds);
        $vals  = array();

        foreach ($cols as $col) {
            $vals[] = "$col = :$col";
        }

        $vals = implode(" AND ", $vals);
        //set attr
        $cols  = array_keys($attr);
        $sets  = array();

        foreach ($cols as $col) {
            $sets[] = "$col = :$col";
        }

        $sets = implode(",", $sets);
        $stm = "UPDATE {$table} SET {$sets} WHERE {$vals}";

        $conds = array_merge($conds,$attr);
        $this->log($stm, $conds);

        try {
            return $this->pdo->fetchAffected($stm, $conds);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }
    protected function e_del($conds)
    {
        if (empty($conds)) {
            return array();
        }
        $table = $this->_table;
        $cols  = array_keys($conds);
        $vals  = array();

        foreach ($cols as $col) {
            $vals[] = "$col = :$col";
        }
        $vals = implode("AND", $vals);
        $stm = "DELETE FROM {$table} WHERE $vals";

        $this->log($stm, $conds);

        try {
            return $this->pdo->fetchAffected($stm, $conds);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }
    protected function exec($stm)
    {
        $this->log($stm);
        try {
            return $this->pdo->fetchAll($stm, array());
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function yieldAll($conds = array(), $fields = array())
    {
        $table = $this->_table;

        if ($fields) {
            $fields = implode(",", $fields);
        } else {
            $fields = "*";
        }

        $stm = "SELECT {$fields} FROM {$table}";

        if ($conds) {
            $cols  = array_keys($conds);
            $vals  = array();
            foreach ($cols as $col) {
                $vals[] = "$col = :$col";
            }

            $vals = implode(" AND ", $vals);
            $stm .= " WHERE  $vals";
        }

        $this->log($stm, $conds);

        try {
            return $this->pdo->yieldAll($stm, $conds);
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function e_begin()
    {
        try {
            return $this->pdo->beginTransaction();
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function e_isbegin()
    {
        try {
            return $this->pdo->inTransaction();
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function e_commit()
    {
        try {
            return $this->pdo->commit();
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }

    protected function e_rollback()
    {
        try {
            return $this->pdo->rollBack();
        } catch (Exception $e)
        {
            throw new Err_Res('Mysql Error ' . $e->getMessage());
        }
    }
}
