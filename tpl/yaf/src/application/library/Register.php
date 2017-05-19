<?php
/**
 * 注册对象
 */

class Register{

	//对象注册池
	private static $model = array();

	/**
     * 注册对象
     * @access public
     * @param  string  $alisa    注册对象别名
     * @param  object  $object   数据对象
     */

	public static function _set($alisa,$object){
		if(isset(self::$model[$alisa])){
			self::_unset($model[$alisa]);
		}
		self::$model[$alisa] = $object;
	}

	/**
     * 获取对象
     * @access public
     * @param  string  $alisa    注册对象别名
     * @return object   		 数据对象
     */

	public static function _get($alisa){
		if(!isset(self::$model[$alisa])){
			return null;
		}
		return self::$model[$alisa];
	}

	/**
     * 删除数据对象
     * @access public
     * @param  string  $alisa    注册对象别名
     */

	public static function _unset($alisa){
		unset(self::$model[$alisa]);
	}
}
