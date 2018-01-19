<?php

namespace frontend\models\Post;

class Options{
	static $options;
	
	public static function setOptions($options){
		self::$options = $options;
	}
	
	public static function get($key = NULL){
		return $key ? self::$options[$key] : self::$options;
	}
	
	public static function front(){
		return self::$options['rewrite']['with_front'];
	}
	
	public static function slug(){
		return self::$options['rewrite']['slug'];
	}
	
	public static function getArchiveSlug(){
		return self::$options['has_archive'] . (self::$options['has_archive'] ? '/' : '');
	}
}