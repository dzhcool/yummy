<?php
/**
 * cache
 */

class Cache{
	private static $ins = null;

	public static function ins(){
		if(empty(self::$ins)){
			$cache_type = Yaf_Registry::get('config')->application->default->cache;
			$class = "Cache_".$cache_type;
			if(class_exists($class)){
				self::$ins = new $class();
			}
		}
		return self::$ins;
	}

	public function __call($method, $args){
		if(method_exists(self::$ins, $method)){
			 return call_user_func_array(array(self::$ins, $method), $args);
		}else{
			Error(Lang('_METHOD_NOT_EXIST_'), 1005);
		}
	}
}
