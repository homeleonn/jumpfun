<?php

namespace Jump\helpers;

class MyDate{
	public static function getDateTime(){
		return date('Y-m-d H:i:s', time());
	}
}