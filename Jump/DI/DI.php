<?php

namespace Jump\DI;

class DI{
	private $container = [];
	private static $_instance;
	
	private function __construct(){}
	public static function getInstance(){
		if(!self::$_instance)
			self::$_instance = new self;
		return self::$_instance;
	}
	
	public function set($dependencyName, $dependency){
		$this->container[$dependencyName] = $dependency;
	}
	
	public function get($dependencyName){
		return $this->has($dependencyName) ? $this->container[$dependencyName] : NULL;
	}
	
	public function has($dependencyName){
		return isset($this->container[$dependencyName]);
	}
	
	
}