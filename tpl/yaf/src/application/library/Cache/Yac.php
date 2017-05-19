<?php
/**
 * yac缓存
 */

class Cache_Yac extends Cache{
	public function __construct(){
		$this->handler = new Yac();
	}

	public function set($name,$args,$ttl = null){
		if(null != $ttl  && is_numeric($ttl)){
			$this->handler->set(md5($name),$args,$ttl);
		}
		$this->handler->set(md5($name),$args);
	}

	public function get($name){
		$data = $this->handler->get(md5($name));
		if(!$data) $data = '';
		return $data;
	}

	public function delete($name,$delay =0){
		$this->handler->delete($name,$delay);
	}

	public function flush(){
		$this->handler->flush();
	}
}
