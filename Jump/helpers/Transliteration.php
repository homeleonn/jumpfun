<?php

namespace Jump\helpers;

class Transliteration{
	public static function run($from){
		if(!is_string($from)){
			throw new \Exception('Type error: variable from is not string');
		}
		
		$ru = ['щ','ш','ч','ц','ю','я','ё','ж','ъ','ы','э','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ь'];
		$en = ['shh','sh','ch','cz','yu','ya','yo','zh','yi','ui','e','a','b','v','g','d','e','z','i','j','k','l','m','n','o','p','r','s','t','u','f','x',''];
		$from = mb_strtolower($from);
		
		foreach($ru as $key => $symbol){
			$from = str_replace($symbol, $en[$key], $from);
		}
		return $from;
	}
}