<?php
/**
 * Yaf 自定义路由协议 Demo
 * 自定义继承 Yaf_Request_Abstract，为的是修改protected params参数
 */

class Router  extends Yaf_Request_Abstract implements Yaf_Route_Interface{
	/**
     * Route 实现，继承实现Yaf_Router_Interface route
     *
     * @access public
	 * @param  Object(Yaf_Request_Http) $req 默认参数
	 * @return boole  true
	 */

	public function route ($req){
		$uri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
		// Test 测试匹配路由为 admin，将模块设定admin
		// URL  路径就成了 /admin/:controller/:action/:params
		if($uri[0] == 'admin'){
			$req->module = 'admin';
			$req->controller = !empty($uri[1])?$uri[1]:'';
			$req->action = !empty($uri[2])?$uri[2]:'';
			if(!empty($uri[3])){
				$param = array();
				$params = array_slice($uri, 3);
				foreach ( $params as $key => $value) {
					if( $key %2 == 0){
						$param[$params[$key]] = $params[$key+1];
					}
				}
				$req->params = $param;
			}
		}
		return true;
	}

	public function assemble (array $mvc, array $query = NULL){
		return true;
	}
}
