<?php
/**
 * 数据库
 */

class DataBase{

	private static $ins = array();
	private static $_ins = NULL;

	public static function ins($config){
		//检测配置文件是否为空
		if(empty($config)) Error(Lang('_NO_DB_CONFIG_'), 1002);
		//生成唯一连接标识
		$key = md5(implode($config, ':'));
		if(!isset(self::$ins[$key])){
			//判断配置类型是否存在
			if(empty($config['type'])) Error(Lang('_NO_DB_TYPE_'), 1002);
			// 查找是否存在
			$class  = "Db_".ucfirst($config['type']);
			// 实例化
			if(class_exists($class)){
				 self::$ins[$key]   =   new $class($config);
			}else{
				Error(Lang('_NO_DB_DRIVER_'), 1003);
			}
		}
		self::$_ins = self::$ins[$key];
		//注册database服务
		Register::_set('db',self::$_ins);
		//返回实例
		return self::$ins[$key];
	}

}
