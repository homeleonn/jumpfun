<?php

namespace Jump\helpers;

class Common{
	
	static $currentPageOptions;
	
	public static function exceptionMessage($e){
		if(isset($e->xdebug_message)){
			echo '<table border="1" cellspacing="0" cellpadding="2">'. $e->xdebug_message. '</table>';
		}else{
			trigger_error($e->getMessage() . ' in ' . $e->getFile() . ' on line: ' . $e->getLine());
		}
	}
	
	public static function loadCurrentPostOptions(){
		if(!self::$currentPageOptions)
			self::$currentPageOptions = HelperDI::get('config')->getCurrentPageOptions();
	}
	
	public static function getConfig($filename){
		return include ROOT . 'Jump/config/' . $filename . '.php';
	}
	
	public static function isPage(){
		return helperDI::get('config')->postType == 'page';
	}
	
	// Строим полную иерархию относительно любого из потомков
	public static function builtHierarchy(&$itemsOnId, &$itemsOnParent, $current, $mergeKey){//var_Dump(func_get_args());exit;
		return self::builtHierarchyDown($itemsOnId, $current, $mergeKey, 0) . '|' . $current[$mergeKey] . '|' . self::builtHierarchyUp($itemsOnParent, $current, $mergeKey, 0);
	}
	
	public static function builtHierarchyDown(&$itemsOnId, $current, $mergeKey, $level = 0){
		if($level > 10) exit('stop recursion');
		$hierarchy = '';
		if(isset($itemsOnId[$current['parent']][0])){
			$next = $itemsOnId[$current['parent']][0];
			$hierarchy = self::builtHierarchyDown($itemsOnId, $next, $mergeKey, $level + 1) . '|' . $next[$mergeKey];
		}
		return $hierarchy;
	}

	public static function builtHierarchyUp1(&$itemsOnParent, $current, $mergeKey, $level = 0){
		if($level > 10) exit('stop recursion');
		$hierarchy = '';
		
		if(isset($itemsOnParent[$current['id']])){
			foreach($itemsOnParent[$current['id']] as $possibleCurrent){
				if($possibleCurrent['id'] == $current['id']){
					$next = $possibleCurrent;
				}
			}
			if(isset($next))
				$hierarchy = $next[$mergeKey] . '|' . self::builtHierarchyUp($itemsOnParent, $next, $mergeKey, $level + 1);
		}
		return $hierarchy;
	}
	
	public static function builtHierarchyUp($itemsOnParent, $current, $postTermsOnId, $mergeKey, $level = 0){//var_dump(func_get_args());exit;
		if($level > 10) exit('stop recursion');
		$hierarchy = '';
		
		if(isset($itemsOnParent[$current['id']])){
			foreach($itemsOnParent[$current['id']] as $possibleNext){
				//var_dump($possibleNext, $current,$postTermsOnId, ';');
				if($possibleNext['parent'] == $current['id'] && isset($postTermsOnId[$possibleNext['id']])){
					$next = $possibleNext;
				}
			}
			if(isset($next))
				$hierarchy = $next[$mergeKey] . '|' . Common::builtHierarchyUp($itemsOnParent, $next, $mergeKey, $level + 1);
		}
		return $hierarchy;
	}
	
	public static function itemsOnKeys($items, $keys){
		if(!is_array($items)){
			throw new \Exception('Argument $items not array');
		}
		if(!is_array($keys)){
			throw new \Exception('Argument $keys not array');
		}
		$itemsOnKey = [];
		foreach($items as $item){
			foreach($keys as $k => $key){
				if(!isset($item[$key])){
					throw new \Exception('Key \'' . $key . '\' is not exists');
				}
				$itemsOnKey[$k][$item[$key]][] = $item;
			}
		}
		return count($keys) == 1 ? $itemsOnKey[0] : $itemsOnKey;
	}
	
	public static function clearDuplicateOnKey($items, $key = 'id'){
		if(!is_array($items)){
			throw new \Exception('Argument $items not array');
		}
		$uniqueItems = [];
		foreach($items as $item){
			if(!isset($item[$key])){
				throw new \Exception('Key \'' . $key . '\' is not exists');
			}
			if(isset($uniqueItems[$item[$key]])) continue;
			$uniqueItems[$item[$key]] = $item;
		}
		return $uniqueItems;
	}
	
	public static function getKeys($array, $key){
		$k = [];
		foreach($array as $a){
			$k[] = $a[$key];
		}
		return $k;
	}
	
	
	
	public static function arrayInCode($array, $arrayName = 'array', $level = 0){
		echo (!$level ? '$' . $arrayName . ' = ' : '') . '[';
		foreach($array as $key => $value){
			if(is_array($value)){
				echo arrayInCode($value, $arrayName, $level + 1);
			}else{
				echo "'{$key}' => '{$value}',";
			}
		}
		echo ']';
		echo !$level ?  ';' : ',';
	}
	
	public static function checkValidation($object, $pattern){
		if(!is_array($object)){
			return preg_match($pattern, $object);
		}else{
			foreach($object as $o){
				if(!preg_match($pattern, $o)){
					return false;
				}
			}
		}
		return true;
	}
	
	public static function termsHTML($taxonomies, $archive){
		if(!is_array($taxonomies)) return false;
		$html = '';
		foreach($taxonomies as $taxName => $terms){
			$html .= "<li>{$taxName}:";
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". SITE_URL . $archive . "{$termLink}/'>{$termName}</a>,";
			}
			$html = substr($html, 0, -1) . '</li>';
		}
		return $html;
	}
	
	public static function archiveTermsHTML($taxonomies, $archive){
		if(!is_array($taxonomies)) return false;
		$html = '';
		foreach($taxonomies as $taxName => $terms){
			$html .= '<div class="filters"><div class="title">' . $taxName . '</div><div class="content">';
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". SITE_URL . $archive . "{$termLink}/'>{$termName}</a><br>";
			}
			$html .= '</div></div>';
		}
		return $html;
	}	
	
	public static function arrayValidation($pattern, $array){
		if(!is_array($taxonomies)) return false;
		$html = '';
		foreach($taxonomies as $taxName => $terms){
			$html .= '<div class="filters"><div class="title">' . $taxName . '</div><div class="content">';
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". SITE_URL . $archive . "{$termLink}/'>{$termName}</a><br>";
			}
			$html .= '</div></div>';
		}
		return $html;
	}	
	
	
	public static function prefix($string, $prefix, $delim = '.'){
		return str_replace($delim, $prefix . $delim, $string);
	}
	
	
	public static function ipCollect(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}
}