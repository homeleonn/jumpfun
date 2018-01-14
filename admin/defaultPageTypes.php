<?php
use Jump\helpers\Common;
// $this->di->get('config')->addPageType([
		// 'type' => 'post',
		// 'title' => 'Записи',
		// 'description' => 'Записи',
		// 'add' => 'Добавить запись',
		// 'edit' => 'Редактировать запись',
		// 'delete' => 'Удалить запись',
		// 'common' => 'записей',
		// 'hierarchical' => true,
		// 'has_archive'  => false,
		// 'taxonomy' => [
			// 'category' => [
				// 'title' => 'Категория',
				// 'add' => 'Добавить категорию',
				// 'edit' => 'Редактировать категорию',
				// 'delete' => 'Удалить категорию',
				// 'hierarchical' => false,
			// ],
		// ],
		// 'rewrite' => ['slug' => '%category%/%postname%', 'with_front' => true, 'paged' => true],
// ]);

$this->di->get('config')->addPageType([
		'type' => 'page',
		'title' => 'Страницы',
		'description' => 'Страницы',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
		'hierarchical' => true,
		'has_archive'  => false,
		'taxonomy' => [],
		//'rewrite' => ['slug' => 'pages', 'with_front' => true, 'paged' => false],
]);

function addFilter($actionName, $funcName){
	if(isset($GLOBALS['jump_actions'][$actionName])){
		foreach($GLOBALS['jump_actions'][$actionName] as $actionFunc){
			if($actionFunc == $funcName) return;
		}
	}
	$GLOBALS['jump_actions'][$actionName][] = $funcName;
}

function applyFilter(){
	$args = func_get_args();
	if(empty($args)) 
		return;
	
	$actionName = array_shift($args);
	
	if(!isset($GLOBALS['jump_actions'][$actionName]))
		return;
	
	foreach($GLOBALS['jump_actions'][$actionName] as $action){
		$args[0] = call_user_func_array($action, $args);
	}
	return($args[0]);
}


//addFilter('postTypeLink', 'jumpPostTypeLink');
function jumpPostTypeLink($link, $post, $terms, $postTermId){
	$structures = [
		'from' => [
			'%postname%',
			'%autor%',
		],
		'to' => [
			$post['url'],
			$post['autor'],
		]
	];
	$link = str_replace($structures['from'], $structures['to'], $link);
	return $link;
}


addFilter('postTypeLink', 'myPostTypeLink');
function myPostTypeLink($link, $post, $terms, $postTerms, $termSlugRelativeSearch){//var_dump(func_get_args());exit;
	if(is_string($postTerms)){
		$formatComponent = $postTerms;
	}else{
		list($termsOnId, $termsOnParent) = Common::itemsOnKeys($terms, ['id', 'parent']);
		$postTermsOnId = Common::itemsOnKeys($postTerms, ['id']);
		if($termSlugRelativeSearch){
			foreach($postTermsOnId as $t){
				if($t[0]['slug'] == $termSlugRelativeSearch){
					$current = $t[0];
					break;
				}
			}
		}
		if(!isset($current))
			$current = $postTermsOnId[array_keys($postTermsOnId)[0]][0];
		$mergeKey = 'slug';
		$formatComponent = str_replace('|', '/', substr(Common::builtHierarchyDown($termsOnId, $current, $mergeKey) . '|' . $current[$mergeKey] . '|' . builtHierarchyUp($termsOnParent, $current, $postTermsOnId, $mergeKey), 1, -1));
	}
	return preg_replace("/%style%/", $formatComponent, $link);
}

function builtHierarchyUp($itemsOnParent, $current, $postTermsOnId, $mergeKey, $level = 0){//var_dump(func_get_args());exit;
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
