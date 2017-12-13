<?php

namespace Jump\DI;

class DI{
	private $container = [];
	
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