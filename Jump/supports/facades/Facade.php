<?php

namespace Jump\supports\facades;

use Jump\helpers\HelperDI;

abstract class Facade{
	protected static function getFacadeAccessor(){
		dd('Facade not found');
	}
	
	public static function __callStatic($name, $arguments){
		if(!$object = HelperDI::get(static::getFacadeAccessor())){
			throw new \Exception('Service \'' . static::getFacadeAccessor() . '\' not found');
		}
		call_user_func_array([$object, $name], $arguments);
	}
}