<?php
namespace Jump\helpers;

class Filter{
    public static function analisys($filters, $rules) {
		$filtersAsString 	= $filters;
		$filters 			= explode(';', $filters);
		
		if($filters[0] == ''){
			return false;
		}
		
		$validFilters 			= self::validation($filters, $rules);
		$validFiltersAsString 	= self::stringFromValidFilters($validFilters);
		
		if(strcmp($filtersAsString, $validFiltersAsString) !== 0){
			header("HTTP/1.1 301 Moved Permanently");
			header('Location:' . str_replace($filtersAsString . (!$validFiltersAsString ? '/' : ''), $validFiltersAsString, FULL_URL));
		}
		
		//var_dump($validFilters);
		//exit;
		return $validFilters;
    }
	
	public static function validation($filters, $rules){
		$filtersNew = [];
		foreach($filters as $key => $value){
			// Проверяем проходит ли валидацию по формату
			if(preg_match('/^([a-zA-Z0-9-]+)=([a-zA-Z0-9-,]+)$/', $value, $match)){
				// Проходит ли валидацию по переданным правилам
				if(isset($rules[$match[1]]) && !preg_match($rules[$match[1]], $match[2])) continue;
				$filtersNew[$match[1]] = $match[2];
			}
		}
		
		return $filtersNew;
	}
	
	public static function sql($filters){
		if(!$filters) return '';
		
		$sqlString = '';
		
		foreach($filters as $filter => $value){
			$sqlString .=  ' and ' . $filter . ' = ' . $value;
		}
		
		return $sqlString;
	}
	
	public static function stringFromValidFilters($validFilters){
		if(!is_array($validFilters)){
			throw new \InvalidArgumentException('invalid filters');
		}
		
		$string = '';
		foreach($validFilters as $filter => $value){
			$string .= $filter . '=' . $value . ';';
		}
		
		return substr($string, 0, -1);
	}
}