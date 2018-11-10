<?php

namespace Jump\supports\facades;

class DB extends Facade{
	protected static function getFacadeAccessor(){
		return 'db';
	}
}