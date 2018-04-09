<?php
use Jump\helpers\Common;
use Jump\helpers\Session;


function plugins($run = false){
	$pluginFolders = glob(ROOT . 'content/plugins/' . '*');
	
	if(!$pluginFolders) return false;
	
	$plugins = [];
	foreach($pluginFolders as $folder)
	{
		$basename = basename($folder);
		$mainFile = $folder . '/' . $basename . '.php';
		
		if(file_exists($mainFile))
		{
			if($run)
				include $mainFile;
			else
				$plugins[] = $mainFile;
		}
	}
	
	return $plugins;
}


$this->di->get('config')->addPageType([
		'type' => 'post',
		'title' => '',
		'title_for_admin' => 'Записи',
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
		'rewrite' => ['slug' => '%category%', 'with_front' => true, 'paged' => 20],
]);

$this->di->get('config')->addPageType([
		'type' => 'page',
		'title' => '',
		'title_for_admin' => 'Страницы',
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
	// if(is_array($userFunc)){
		// if(isset($userFunc[0]) && isset($userFunc[1])){
			// if(is_object($userFunc[0]) && method_exists($userFunc[0], $userFunc[1])){
				// $userFunc[0]->{$userFunc[1]}();
			// }
		// }
		// dd($userFunc);
	// }
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


function vd(){
	$trace = debug_backtrace()[1];
	echo '<small style="color: green;"><pre>',$trace['file'],':',$trace['line'],':</pre></small>';
	call_user_func_array('var_dump', func_get_args()[0] ?: [NULL]);
}

function d(){
	vd(func_get_args());
}

function dd(){
	global $di, $start;
	vd(func_get_args());
	echo '<div style="display: table;clear:both;float:none;"></div>';
	var_dump($di->get('db')->getStats(), 'Время обработки скрипта: ' . substr((microtime(true) - $start), 0, 6));
	exit;
}

function session(){
	$args = func_get_args();
	if(empty($args)){
		return Session::get();
	}elseif(is_string($args[0]) && !isset($args[1])){
		return Session::get($args[0]);
	}else{
		call_user_func_array(['Session', 'set'], $args);
	}
}

function isAdmin(){
	return (int)session('user.accesslevel');
}

function isAuthorized(){
	return session('id');
}

function inAdmin(){
	return ENV == 'admin';
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
function myPostTypeLink($link, $termsOnId, $termsOnParent, $postTerms){//dd(func_get_args());
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


addAction('admin_post_options_form', 'my_admin_post_options_form');
function my_admin_post_options_form(){
	$boxes = [
		['for' => '#post-properties', 	'text' => 'Свойства страницы', 'checked' => 'checked',],
		['for' => '#post-images', 		'text' => 'Изображение страницы', 'checked' => 'checked',],
		['for' => '.extra-fields', 		'text' => 'Произвольные поля', 'checked' => 'checked',],
		['for' => '#post-discussion', 	'text' => 'Обсуждение', 'checked' => 'checked',],
		['for' => '#post-comments', 	'text' => 'Комментарии', 'checked' => 'checked',],
	];
	
	echo '<div id="post-options-box">';
	foreach($boxes as $box){
		echo '<label><input type="checkbox" data-for="',$box['for'],'" ',$box['checked'],'> ',$box['text'],'</label>', "\n";
	}
	echo '<div>';
}

function addPostImgForm($img = false){
	$src = $id = $none = $del = '';
	if($img){
		$src = UPLOADS.$img['src'];
		$id  = $img['id'];
	}else{
		$none  = 'none';
		$del = 'none';
	}
	?>
	<div id="post-images" class="side-block">
		<div class="block-title">Изображение страницы</div>
		<div class="block-content">
			<span class="icon-plus" id="add-post-img"></span>
			<span class="icon-cancel red cancel <?=$del?>"></span>
			<div id="post-img-container" class="<?=$none?>"><img src="<?=$src?>" class="shower"></div>
			<input class="none-impt" type="hidden" name="_jmp_post_img" value="<?=$id?>">
		</div>
	</div>
	<div id="alpha-back" class="none">
		<div id="media-modal"></div>
	</div>
	
	<?php
}


function getMenu(){
	$cacheFileName = 'menu/menu';
	if(Common::getCache($cacheFileName, -1)) return;
	
	global $di;
	$db = $di->get('db');
	$cats = $db->getAll('Select * from menu where menu_id = '.Common::getOption('menu_active_id').' ORDER BY sort, parent');
	if(!$cats) return;
	$newCats = array(
		'cats' => array(),
		'subCats' => array()
	);
	
	/*формируем из все категорий - главные категории и подкатегории*/
	foreach($cats as $cat){
		if($cat['parent'] == -1)
			$newCats['cats'][] = $cat;
		else
			$newCats['subCats'][$cat['parent']][] = $cat;
	}
	
	/*Очищаем изначальные категории, которые были в перемешку*/
	unset($cats);
	
	/*Начинаем выводить меню, первым пунктом статично поставим главную страницу*/
	?>
	<nav>
		<div class="menu-toggle" onclick="$(this).next().toggleClass('show')"><div></div><div></div><div></div></div>
		<ul class="menu"><li><a href="<?=SITE_URL?>">Главная</a></li>
	<?php
	/*Пройдемся по всем главнм категориям*/
	foreach($newCats['cats'] as $cat){
		$issetSubMenu = isset($newCats['subCats'][$cat['object_id']]);
		
		/*Проходим по подкатегориям, сохраняя их для вывода*/
		if($issetSubMenu){
			$subCatsView = '';
			foreach($newCats['subCats'][$cat['object_id']] as $subCat){
				$currentSubCatUrl = strpos($subCat['url'], 'http') === 0 ? $subCat['url'] : SITE_URL . "{$subCat['url']}/";
				$subCatsView .= "<li><a href=\"{$currentSubCatUrl}\">{$subCat['name']}</a></li>";
			}
		}
		
		?>
		<li class="top-menu">
			<?php echo "<a href=\"".($issetSubMenu ? 'javascript:void(0);' : (strpos($cat['url'], 'http') === 0 ? $cat['url']:SITE_URL."{$cat['url']}/"))."\">{$cat['name']}</a>";?>
			<?php if(!$issetSubMenu) {echo '</li>'; continue;}?>
			<ul class="submenu"><?=$subCatsView?></ul>
		</li>
		<?php
	}
	echo '</ul></nav>';
	
	echo Common::setCache($cacheFileName);
}


function route($needRoute){
	global $di;
	$findRoute = false;
	foreach($di->get('router')->routes as $route)
		if(isset($route[$needRoute])){
			$findRout = ROOT_URI . $route[$needRoute]['controller'];
			break;
		}
		
	if(!$findRoute)
		throw new Exception('Route not found');
	dd($findRoute);
	return $findRoute;
}

function uri($path){
	return ROOT_URI . (inAdmin() ? 'admin/' : '') . $path . '/';
}

function redirect($path){
	global $di;
	$di->get('request')->location(ROOT_URI . ($path ? $path . '/' : ''));
}





// $s = 'category/news/politics/page/2/';
// $pattern = '~^category/(.*)/page/([2-9]|\d{2,})/$~u';

// dd($s, $pattern, preg_match($pattern, $s, $matches), $matches);


