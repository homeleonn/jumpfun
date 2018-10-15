<?php

namespace Jump\DI;

class DI{
	use \Jump\traits\Singletone;
	public $container = [];
	
	private function __construct(){}
	
	public function set($dependencyName, $dependency){
		$this->container[$dependencyName] = $dependency;
	}
	
	public function get($dependencyName){
		return $this->has($dependencyName) ? $this->container[$dependencyName] : NULL;
	}
	
	public function has($dependencyName){
		return isset($this->container[$dependencyName]);
	}
	
	public static function getD($dependencyName){
		return self::getInstance()->get($dependencyName);
	}
}