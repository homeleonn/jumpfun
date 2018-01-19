<?php

namespace Jump\core\cache;

class Cache{
	
	private static $cache;
	
	private function __construct(){}
	
	public static function get($key){
		return isset(self::$cache[$key]) ? self::$cache[$key] : NULL;
	}
	
	public static function set($key, $value){
		self::$cache[$key] = $value;
	}
}