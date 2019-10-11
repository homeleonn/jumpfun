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
		if(empty($itemsOnKey)) return false;
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
	
	public static function getKeys($array, $key, $distinct = false){
		$k = [];
		foreach($array as $a){
			if($distinct){
				if(!isset($k[$a[$key]]))
					$k[$a[$key]] = $a[$key];
			}
			else
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
				$html .= " <a href='". SITE_URL . langUrl() . $archive . "{$termLink}/'>{$termName}</a>,";
			}
			$html = substr($html, 0, -1) . '</li>';
		}
		return '<ul class="terms">' . $html . '</ul>';
	}
	
	public static function archiveTermsHTML($taxonomies, $archive){
		if(!is_array($taxonomies)) return false;
		$html = '';
		foreach($taxonomies as $taxName => $terms){
			$html .= '<div class="filters"><div class="title">' . $taxName . '</div><div class="content">';
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". SITE_URL . langUrl() . $archive . "{$termLink}/'>{$termName}</a>";
			}
			$html .= " <a href='". SITE_URL . langUrl() . $archive . "'>Все</a>";
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
	
	public static function log(){
		if(!defined('LOG_ON') || !LOG_ON) return;
		$logPath = ROOT . 'content/uploads/logs/stats.log';
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'NULL';
		$data = 
			$_SERVER['REQUEST_METHOD']  . ' :: ' .
			$_SERVER['REQUEST_URI'] 	. ' <- ' .
			$ref 						. ' :: ' .
			$_SERVER['HTTP_USER_AGENT'] . ' :: ' .
			self::ipCollect() 			. ' :: ' .
			date('Y.m.d H:i:s') 			. "\n";
		
		if(!file_exists($logPath)){
			$f = fopen($logPath, 'w');
			fclose($f);
		}
			
		self::write($logPath, $data, 'a+');
	}
	
	
	public static function write($fname, $data, $mode = 'w'){
		$f = fopen($fname, $mode);
		flock($f, LOCK_EX);
		fwrite($f, $data);
		flock($f, LOCK_UN);
		fclose($f);	
	}
	
	
	
	public static function getOption($key, $unsrlz = false){
		$option = HelperDI::get('config')->getOption($key);
		return $unsrlz ? unserialize($option) : $option;
	}
	
	public static function setOption($key, $value, $ser = false){
		$optionsFileName = JUMP . '/config/options.php';
		$options = file_get_contents($optionsFileName);
		
		if ($ser) $value = serialize($value);
		
		$optionPattern = '~(\''.$key.'\'\s*=>\s*)\'(.*)\'~';
		$optionReplace = '$1\''.$value.'\'';
		$newOption = "\t'{$key}' => '{$value}',\r\n]";
		
		$newOptions = preg_match($optionPattern, $options) ? preg_replace($optionPattern, $optionReplace, $options) :
															 preg_replace('~]~', $newOption, $options);
		
		$config = HelperDI::get('config');
		$config->setOption($key, $value);
		file_put_contents($optionsFileName, $newOptions);
	}
	
	public static function getCache($cacheFileName, $delay = 86400, $outNow = true){
		if(!cacheIsEnable()) return false;
		$cacheFileName = UPLOADS_DIR . 'cache/' . $cacheFileName . '.html';
		
		if(file_exists($cacheFileName))
		{
			if($delay == -1 || (filemtime($cacheFileName) > time() - $delay)){//d(1, cacheIsEnable());
				if(($data = file_get_contents($cacheFileName)) === FALSE) return false;
				
				if($data != ''){
					if($outNow){
						echo $data;
					}else{
						return $data;
					}   
					return true;
				}
			}
		}
		ob_start();
		return false;
	}

	public static function setCache($cacheFileName, $data = false){
		if(!$data) $data = ob_get_clean();
		if(!cacheIsEnable()) return $data;
		$cacheFileName = UPLOADS_DIR . 'cache/' . $cacheFileName . '.html';
		
		if(!is_dir($dir = dirname($cacheFileName))){
			mkdir($dir, 0755, true);
		}
		
		file_put_contents($cacheFileName, $data, LOCK_EX);
		return $data;
	}
	
	public static function clearCache($cacheFileName){
		if(is_file($cacheFileName = UPLOADS_DIR . 'cache/' . $cacheFileName . '.html')){
			unlink($cacheFileName);
		}
	}
	
	
	public static function className($namespace){
		return end(explode('\\', $namespace));
	}
	
	public static function textSanitize($content, $type = 'content', $tagsOn = false){
		$types = [
			'all' => [
				'from' 	=> ['<?', '<?php', '<%'],
				'to' 	=> ['']
			],
			'content' => [
				'from' 	=> [],
				'to' 	=> []
			],
			'title' => [
				'from' 	=> ['\'', '"'],
				'to' 	=> ['’', '»']
			],
		];
		if(!isset($types[$type])) $type = 'content';
		$content = str_replace($types[$type]['from'], $types[$type]['to'], str_replace($types['all']['from'], $types['all']['to'], $content));
		if(!$tagsOn)
			$content = htmlspecialchars($content);
		if($type == 'content')
			$content = html_entity_decode($content);
		
		return $content;
	}
}