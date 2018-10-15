<?php

namespace Jump\traits;

trait Singletone{
	private static $instance;
	
	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new self;
		
		return self::$instance;
	}
}