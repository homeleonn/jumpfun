<?php

namespace Jump\supports\facades;

use Jump\helpers\HelperDI;

abstract class Facade{
	protected static function getFacadeAccessor(){
		dd('Facade not found');
	}
	
	public static function __callStatic($name, $arguments){
		if(!$object = HelperDI::get(static::getFacadeAccessor())){
			// $provider = '\Jump\services\\' . ucfirst(static::getFacadeAccessor()) . 'Provider';
			// if($provider = new $provider(HelperDI::get())){
				// $provider->init();
				// $object = HelperDI::get(static::getFacadeAccessor());
				// var_dump($object, $name);
			// }else{
				// throw new \Exception('Service \'' . static::getFacadeAccessor() . '\' not found');
			// }
			
			throw new \Exception('Service \'' . static::getFacadeAccessor() . '\' not found');
		}
		call_user_func_array([$object, $name], $arguments);
	}
}