<?php
/**
 * Model模型类
 * 实现了ORM
 */

class Model{

	// 当前数据表名称
	protected $tableName = NULL;
	// 当前数据库操作对象
    protected $db        = NULL;
    // 数据库对象池
	private   $_db		 = array();
	// 查询选项
	protected $options   = array();
	// __call查询方法
	protected $method    = array('distinct','field','join','where','group','having','order','limit','union','lock','force');
	// 数据表前缀
	protected $prefix 	 = '';

	public function __construct($tableName='',$config=''){
		// 用于Model初始化方法
		$this->_init();
		//获取数据库配置
		if(is_string($config) || empty($config)) {
			//如果是字符串从配置文件读取
			if(empty($config)) $config = 'default';
			$config = Yaf_Registry::get('config')->database->$config->toArray();
		}
		//获取表前缀
		$this->prefix = $prefix = isset($config['prefix']) ? $config['prefix'] : '';
		//获取数据表名称
		if(!empty($tableName)) $this->tableName = $tableName;
		//new Model的时候使用
		if($this->tableName == NULL) $this->tableName = substr(get_class($this), 0,-5);
		$this->tableName = $prefix.strtolower(ltrim($this->tableName,$prefix));
		//切换数据库连接
		$this->link(0,empty($this->config)?$config:$this->config,true);
		$this->db->truetable = $this->tableName;
	}

	/**
     * 切换数据库连接
     * @access public
     * @param  integer $linkNum  连接序号
     * @param  mixed   $config   数据库连接信息
     * @param  boolean $force    强制重新连接
     * @return Model
     */

	public function link($linkNum='',$config='',$force=false){
		//判断是否需要重新连接
		if(!isset($this->_db[$linkNum]) || $force){
			$this->_db[$linkNum] = Database::getInstance($config);
		}
		//取得当前的数据库对象
		$this->db = $this->_db[$linkNum];
	}

	public function getFields(){
		return $this->db->getFields($this->tableName);
	}

	/**
     * 查询一条数据
     * @access public
     * @return result
     */

	public function findOne(){
		$this->db->limit(1);
		$result = $this->db->select();
		return $result;
	}

	/**
     * 插入数据
     * @access public
     * @return lastid
     */

	public function add($array){
		if(!is_array($array)){
			new Yaf_Exception("数据类型不对");
		}
		return $this->db->insert($array);
	}

	/**
     * 更新数据
     * @access public
     * @return lastid
     */

	public function update($array){
		if(!is_array($array)){
			new Yaf_Exception("数据类型不对");
		}
		return $this->db->update($array);
	}

	/**
     * 插入数据
     * @access public
     * @return lastid
     */

	public function delete(){
		return $this->db->delete();
	}

	/**
     * 返回所有的结果集
     * @access public
     * @return result
     */

	public function select(){
		$result = $this->db->select();
		return $result;
	}

	/**
     * 指定当前的数据表
     * @access public
     * @param mixed $table
     * @return Model
     */

    public function table($table='') {
        $prefix =   $this->prefix;
     	if(is_string($table) && !empty($table)){
     		if(stristr($table,$prefix)){
     			$this->db->truetable = $table;
     		}else{
     			$this->db->truetable = $prefix.$table;
     		}
     	}
        return $this;
    }

	/**
     * 使用__call方法实现连贯操作
     * @access public
     * @return result
     */

	public function __call($method,$args){
		$options = strtolower($method);
		if(in_array($options,$this->method)){
			if(empty($args[0])) $args[0] ='';
			$this->db->$options($args[0]);
			return $this;
		}else{
			Error(Lang('_METHOD_NOT_EXIST_'),1005);
		}
	}

	public function _init(){}
}
