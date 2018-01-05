<?php

$sections = array(
	'Главная панель||gauge' => '',
	'Страницы||' => [
		'Все страницы' => 'pages',
		'Добавить новую' => 'pages/add',
	],
	'Педагоги||' => [
		'Все педагоги' => 'educators',
		'Добавить новую' => 'educators/add',
		'Стили' => 'educators/terms/?term=style',
		'Возраст' => 'educators/terms/?term=age',
	],
	'Новости||' => [
		'Все новости' => 'news',
		'Добавить новую' => 'news/add',
	],
	'Настройки||cog' => [
		'Меню' => 'menu',
	],
);

function menu1($menu, $title = 0, $stage = 0){
	$menuStr = '';
	$selfStage = $stage + 1;
	if($stage){
		if(!is_numeric($title)) 
			$menuStr .= drawLink(false, $title)."\n";
		$menuStr .= '<ul class="submenu'.$stage.'">'."\n";
	}else{
		$menuStr .= '<ul id="menu">'."\n";
	}
	
	foreach($menu as $key => $val){
		if(!is_array($val)){
			$menuStr .= !$stage ? '<li class="top">':'';
			$parent = $stage ? false : 2;
			$menuStr .= drawLink($val, $key, $parent)."\n";
			$menuStr .= !$stage ? '</li>':'';
		}else{
			$menuStr .= '<li class="top">' . menu1($val, $key, $selfStage) . '<li>';
		}
	}
	$menuStr .=  '</ul>';
	
	return $menuStr;
}

function drawLink($path, $name, $parent = 1){
	if($path && strpos($path, '?') === false) $path .= '/';
	$path = $path !== false ? SITE_URL.'admin/'.$path : 'javascript:void(0);';
	
	if($parent){
		$name = explode('||', $name);
		$name[1] = !isset($name[1]) ? '' : 'class="icon-'.$name[1].'"';
		$supParent = ($parent == 1) ? '<span></span>' : '';
	}else{
		$name = explode('|', $name);
		$add = (isset($name[1]) ? $name[1] : '');
		$name = $name[0];
	}
	
	return $parent ? '<div class="title"><a href="'.$path.'" '.$name[1].' data-menu="1">'.$name[0].$supParent.'</a></div>'
					 : '<li>'.($add ? '<a class="add icon-plus" href="'.$path.$add.'"></a>':'').'<a href="'.$path.'">'.$name.'</a><div class="clearfix"></div></li>' ;
}

echo menu1($sections);