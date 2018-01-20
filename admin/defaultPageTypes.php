<?php
use Jump\helpers\Common;
$this->di->get('config')->addPageType([
		'type' => 'post',
		'title' => 'Записи',
		'description' => 'Записи',
		'add' => 'Добавить запись',
		'edit' => 'Редактировать запись',
		'delete' => 'Удалить запись',
		'common' => 'записей',
		'hierarchical' => false,
		'has_archive'  => false,
		'taxonomy' => [
			'category' => [
				'title' => 'Категория',
				'add' => 'Добавить категорию',
				'edit' => 'Редактировать категорию',
				'delete' => 'Удалить категорию',
				'hierarchical' => false,
			],
		],
		'rewrite' => ['slug' => '%category%', 'with_front' => true, 'paged' => true],
]);

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


function add($type, $funcName, $userFunc){
	$GLOBALS['jump_'.$type][$funcName][] = $userFunc;
}

function addAction($actionName, $userFunc){
	add('actions', $actionName, $userFunc);
}

function addFilter($filterName, $userFunc){
	add('filters', $filterName, $userFunc);
}

function apply(){
	$args = func_get_args();
	if(empty($args)) 
		return;
	
	$type = 'jump_' . array_shift($args);
	if(!count($args)) return;
	$funcName = array_shift($args);
	
	if(!isset($GLOBALS[$type][$funcName]))
		return;
	
	$isfilters = $type == 'jump_filters';
	
	foreach($GLOBALS[$type][$funcName] as $key => $filter){
		$result = call_user_func_array($filter, $args);
		if($isfilters){
			$args[0] = $result;
		}
	}
	
	return isset($args[0]) ? $args[0] : false;
}

function doAction(){
	call_user_func_array('apply', array_merge(['actions'], func_get_args()));
}
function applyFilter(){
	return call_user_func_array('apply', array_merge(['filters'], func_get_args()));
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
function myPostTypeLink($link, $termsOnId, $termsOnParent, $postTerms){//var_dump(func_get_args());exit;
	$replaceFormat = '/%.*%/';
	if(!preg_match($replaceFormat, $link)) return $link;
	if(!$postTerms){
		$formatComponent = 'uncategorized';
	}elseif(is_string($postTerms)){
		$formatComponent = $postTerms;
	}else{
		$postTermsOnId = Common::itemsOnKeys($postTerms, ['id']);
		$current = $postTermsOnId[array_keys($postTermsOnId)[0]][0];
		$mergeKey = 'slug';
		$formatComponent = str_replace('|', '/', substr(Common::builtHierarchyDown($termsOnId, $current, $mergeKey) . '|' . $current[$mergeKey] . '|' . Common::builtHierarchyUp($termsOnParent, $current, $postTermsOnId, $mergeKey), 1, -1));
	}
	return preg_replace($replaceFormat, $formatComponent, $link);
}

function getTermsByPostId($postId){
	return isset(Cache::get('postTerms')[$postId]) ? Cache::get('postTerms')[$postId] : null;
}

function getTermsByTaxonomies(){
	return Cache::get('allTerms');
}

//var_dump(getTermsByPostId(70));
function jmpHead(){
	doAction('jhead');
}

addAction('add_extra_rows', 'my_add_extra_rows');
function my_add_extra_rows($postType){
	if($postType != 'post') return;
}



function getExtraField($index, $name, $value){
	?>
	<div class="field mtop10">
		<div class="row">
			<div class="col-md-4">
				<input type="text" class="extra_name w100" value="<?=$name?>">
				<div class="mtop10">
					<input class="extra_field_delete" data-extra_index="<?=$index?>" type="button" value="Удалить">
					<input class="extra_field_update" data-extra_index="<?=$index?>" type="button" value="Обновить">
				</div>
			</div>
			<div class="col-md-8">
				<textarea name="extra_fileds[<?=$name?>]" class="w100" rows="2"><?=$value?></textarea>
			</div>
		</div>
	</div>
	<?php
}