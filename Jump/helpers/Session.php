<?php
namespace Jump\helpers;

class Session
{
	public static function get($key = NULL)
	{
		if($key == NULL) return $_SESSION;
		
		if(strpos($key, '.') !== FALSE)
		{
			$tmp = [];
			$level = false;
			
			foreach(explode('.', $key) as $key)
			{
				if(!$level)
				{
					if(!isset($_SESSION[$key])) return NULL;
					$level = true;
					$tmp = $_SESSION[$key];
				}
				else
				{
					if(!isset($tmp[$key])) return NULL;
					$tmp = $tmp[$key];
				}
			}
			
			return $tmp;
		}
		
		return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
	}
	
	public static function set(){
		$args = func_get_args();
		if(!is_string($args[0]) && !is_array($args[0])){
			throw new \Exception('First argument must be a string or an array. ' . gettype($args[0]) . ' given.');
		}
		
		$tmp = [];
		if(is_array($args[0]))
		{
			foreach($args as $value)
			{
				if(!is_array($value)) self::InvalidArgumentsException();
				
				foreach($value as $key => $v)
				{
					if(is_int($key)) self::InvalidArgumentsException();
					$tmp[$key] = $v;
				}
			}
			$_SESSION = array_merge($_SESSION, $tmp);
		}else{
			if(!isset($args[1])) self::InvalidArgumentsException();
			$_SESSION[$args[0]] = $args[1];
		}
	}
	
	public static function push($key, $value){
		if(isset($_SESSION[$key])) $_SESSION[$key] = array_merge($_SESSION[$key], $value);
	}
	
	public static function delete($key = NULL){
		if($key == NULL) $_SESSION = [];
		if(isset($_SESSION[$key])) unset($_SESSION[$key]);
	}
	
	private static function InvalidArgumentsException(){
		 throw new \Exception('Invalid arguments');
	}
}