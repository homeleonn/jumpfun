<?php

use Jump\helpers\Common;
use Jump\helpers\Session;
use Jump\DI\DI;

/**
 *  Search plugins and run them
 *  
 *  @param array $activePlugins
 *  
 *  @return array paths to main file of plugins
 */
function plugins(array $activePlugins = []):array
{
	static $activated = false;
	
	$pluginsRootFolder = ROOT . 'content/plugins/';
	$pluginFolders = glob($pluginsRootFolder . '*');
	
	if(!$pluginFolders) return false;
	
	$plugins = [];
	foreach($pluginFolders as $folder)
	{
		$basename = basename($folder);
		$mainFile = $folder . '/' . $basename . '.php';
		if(file_exists($mainFile))
		{
			$pluginPath = str_replace($pluginsRootFolder, '', $mainFile);
			$isActive   = in_array($pluginPath, $activePlugins);
			
			if(!$activated && $isActive){
				include $mainFile;
			}
			
			$plugins[] = ['src' => $mainFile, 'active' => $isActive, 'path' => $pluginPath];
		}
	}
	
	$activated = true;
	return $plugins;
}



function add($type, $funcName, $userFunc, $front = false){
	// if(is_array($userFunc)){
		// if(isset($userFunc[0]) && isset($userFunc[1])){
			// if(is_object($userFunc[0]) && method_exists($userFunc[0], $userFunc[1])){
				// $userFunc[0]->{$userFunc[1]}();
			// }
		// }
		// dd($userFunc);
	// }
	if($front){
		if(!isset($GLOBALS['jump_'.$type][$funcName]))
			$GLOBALS['jump_'.$type][$funcName] = [];
		
		array_unshift($GLOBALS['jump_'.$type][$funcName], $userFunc);
	}else{
		$GLOBALS['jump_'.$type][$funcName][] = $userFunc;
	}
	
}

function addAction($actionName, $userFunc, $front = false){
	add('actions', $actionName, $userFunc, $front);
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
		return isset($args[0]) ? $args[0] : false;
	
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
	echo '<small style="color: green;"><pre>',$trace['file'],':',$trace['line'],':</pre></small><pre>';
	call_user_func_array('var_dump', func_get_args()[0] ? [func_get_args()[0]]: [NULL]);
}

function d(){
	vd(func_get_args());
}

function dd(){
	vd(func_get_args());
	requestStats();
	exit;
}

function requestStats(){
	global $di, $start;
	echo '<div style="display: table;clear:both;float:none;"></div><hr>';
	$dbStats = $di->get('db')->getStats();
	vd(!is_null($dbStats) ? $dbStats: 'Подключения и запросы к БД не производились', 'Время обработки скрипта: ' . substr((microtime(true) - $start), 0, 6));
}

function session(){
	$args = func_get_args();
	if(empty($args)){
		return Session::get();
	}elseif(is_string($args[0]) && !isset($args[1])){
		return Session::get($args[0]);
	}else{
		call_user_func_array('Jump\helpers\Session::set', $args);
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
	global $post;
	$post = applyFilter('jhead', $post);
echo <<<EOT
<title>{$post['title']}</title>\n\t
EOT;
	doAction('jhead');
}



addAction('add_extra_rows', 'my_add_extra_rows');
function my_add_extra_rows($postType){
	if($postType != 'post') return;
}


function watermark($photo, $watermark, $to){
	$im = imagecreatefromjpeg($photo);
	$stamp = imagecreatefrompng($watermark);
	$size = getimagesize($photo);
	$sx = imagesx($stamp);
	$sy = imagesy($stamp);

	imagecopymerge_alpha($im, $stamp, 0, 0, 0, 0, $sx, $sy, 50);

	imagejpeg($im, $to);
	imagedestroy($im);
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
	// creating a cut resource
	$cut = imagecreatetruecolor($src_w, $src_h);

	// copying relevant section from background to the cut resource
	imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
   
	// copying relevant section from watermark to the cut resource
	imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
   
	// insert cut resource to destination image
	imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
} 


function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px) {
    for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
        for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
            $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);
   return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}


function addPrefix($string, $prefix, $delim = '.'){
	return Common::prefix($string, $prefix, $delim);
}

function test($type = 1){

	global $di;
	// $di->get('db')->query('ALTER TABLE media ADD meta longtext NOT NULL');
	$imgs = $di->get('db')->getAll('Select * from media');
	//dd(dirname($imgs[0]['src']));
	$i = 0;
	ini_set('gd.jpeg_ignore_warning', 1);
	foreach($imgs as $img){
		$fullPath = UPLOADS_DIR . $img['src'];
		$sizes = getimagesize($fullPath);
		$imgPathParts = pathinfo($img['src']);
		$mediumW = 320;
		$mediumH = 320;
		$data = [
			'width' => $sizes[0],
			'height' => $sizes[1],
			'dir' => $imgPathParts['dirname'] . '/',
			'sizes' => [
				'thumbnail' => [
					'file' => addPrefix($imgPathParts['basename'], '-150x150'),
					'width' => 150,
					'height' => 150,
					'mime' => $sizes['mime'],
				]
			]
		];
		
		if($sizes[0] >= $sizes[1]){
			$ratio = $mediumW / $sizes[0];
			$newW = $mediumW;
			$newH = (int)round($sizes[1] * $ratio);
		}else{
			$ratio = $mediumH / $sizes[1];
			$newW = (int)round($sizes[0] * $ratio);
			$newH = $mediumH;
		}
		
		
		$data['sizes']['medium'] = [
			'file' => addPrefix($imgPathParts['basename'], "-{$newW}x{$newH}"),
			'width' => $newW,
			'height' => $newH,
			'mime' => $sizes['mime'],
		];
		
		$image_p = imagecreatetruecolor($newW, $newH);
		
		$meta = unserialize($img['meta']);
		
		if(isset($meta['sizes']['medium'])){
			unlink(UPLOADS_DIR . $data['dir'] . $meta['sizes']['medium']['file']);
		}
		$imageParams = [$image_p, UPLOADS_DIR . $meta['dir'] . $data['sizes']['medium']['file']];
		
		$imgType = explode('/', $sizes['mime'])[1];
		
		ob_start();
		$image = call_user_func('imagecreatefrom' . $imgType, $fullPath);
		ob_end_clean();
		
		
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newW, $newH, $sizes[0], $sizes[1]);
		
		$alpha = ['png', 'gif'];
		if(in_array($imgType, $alpha)){
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}else{
			$imageParams[] = 80; // quality
		}
		
		
		call_user_func_array('image' . $imgType, $imageParams);dd();
		$di->get('db')->query('Update media SET meta = \''.serialize($data).'\' where id = ' . $img['id']);
		
		
	}dd();
}

//test();


function resizeOriginalPhoto(){
	global $di;
	// $di->get('db')->query('ALTER TABLE media ADD meta longtext NOT NULL');
	$imgs = $di->get('db')->getAll('Select * from media');
	//dd(dirname($imgs[0]['src']));
	$i = 0;
	ini_set('gd.jpeg_ignore_warning', 1);
	foreach($imgs as $img){d($img, unserialize($img['meta']));continue;
		$fullPath = UPLOADS_DIR . $img['src'];
		$sizes = getimagesize($fullPath);
		$image_p = imagecreatetruecolor($sizes[0], $sizes[1]);
		
		$imageParams = [$image_p, $fullPath];
		
		$imgType = explode('/', $sizes['mime'])[1];
		
		ob_start();
		$image = call_user_func('imagecreatefrom' . $imgType, $fullPath);
		ob_end_clean();
		
		
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $sizes[0], $sizes[1], $sizes[0], $sizes[1]);
		
		$alpha = ['png', 'gif'];
		if(in_array($imgType, $alpha)){
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}else{
			$imageParams[] = 70; // quality
		}
		
		
		//call_user_func_array('image' . $imgType, $imageParams);d($fullPath);
	}
}

//resizeOriginalPhoto();

function test1(){
	dd(unserialize('a:3:{s:5:"width";i:1399;s:6:"height";i:791;s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:23:"AOD5D9by1O3-150x150.jpg";s:5:"width";i:150;s:6:"height";i:150;s:4:"mime";s:10:"image/jpeg";}s:6:"medium";a:4:{s:4:"file";s:23:"AOD5D9by1O3-300x170.jpg";s:5:"width";i:300;s:6:"height";i:170;s:4:"mime";s:10:"image/jpeg";}}}'));
}

//test1();

function postImgSrc($post, $thumbnail = 'orig'){
	$validKeys = ['thumbnail', 'medium'];
	
	if(in_array($thumbnail, $validKeys) && isset($post['_jmp_post_img_meta']['sizes'][$thumbnail])){
		return UPLOADS . substr($post['_jmp_post_img'], 0, strrpos($post['_jmp_post_img'], '/') + 1) . $post['_jmp_post_img_meta']['sizes'][$thumbnail]['file'];
	}
	
	return isset($post['_jmp_post_img']) ? UPLOADS . $post['_jmp_post_img'] : THEME . 'img/002.jpg';
}


function test2(){
	global $di;
	$media = $di->get('db')->getAll('Select * from media');
	$ins = '';
	foreach($media as $m){
		$m['meta'] = unserialize($m['meta']);
		$m['meta']['dir'] = '2018/08/';
		foreach($m['meta']['sizes'] as &$s){
			$s['file'] = str_replace($m['meta']['dir'], '', $s['file']);
		}
		$m['meta'] = serialize($m['meta']);
		$di->get('db')->query("Update media SET meta = '{$m['meta']}' where id = {$m['id']}");
	}
	dd();
}
//test2();

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

function token($token = false){
	if($token)
		return isset($_POST['token']) ? $token == session('token') : false;
	
	$token = md5(uniqid(rand(), TRUE));
	session('token', $token);
	return $token;
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

function sessStart(){
	if(!isset($_SESSION)){
		session_start();
	}
}


function getMenu(){
	$cacheFileName = 'menu/menu';
	if(Common::getCache($cacheFileName, -1)) return;
	
	$cats = DI::getD('db')->getAll('Select * from menu where menu_id = '.Common::getOption('menu_active_id').' ORDER BY sort, parent');
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
	<nav class="menu">
		<label for="mobile-nav"><div></div></label>
		<input type="checkbox" id="mobile-nav">
		<ul class="menu"><li><a href="<?=ROOT_URI?>">Главная</a></li>
	<?php
	/*Пройдемся по всем главнм категориям*/
	foreach($newCats['cats'] as $cat){
		$issetSubMenu = isset($newCats['subCats'][$cat['object_id']]);
		
		/*Проходим по подкатегориям, сохраняя их для вывода*/
		if($issetSubMenu){
			$subCatsView = '';
			foreach($newCats['subCats'][$cat['object_id']] as $subCat){
				$currentSubCatUrl = strpos($subCat['url'], 'http') === 0 ? $subCat['url'] : ROOT_URI . "{$subCat['url']}/";
				$subCatsView .= "<li><a href=\"{$currentSubCatUrl}\">{$subCat['name']}</a></li>";
			}
		}
		
		?>
		<li class="top-menu">
			<?php echo "<a href=\"".($issetSubMenu ? 'javascript:void(0);' : (strpos($cat['url'], 'http') === 0 ? $cat['url']:ROOT_URI."{$cat['url']}/"))."\">{$cat['name']}</a>";?>
			<?php if(!$issetSubMenu) {echo '</li>'; continue;}?>
			<ul class="submenu"><?=$subCatsView?></ul>
		</li>
		<?php
	}
	echo '
	<li class="top-menu hidd extra-contacts">
		<div>
			<a href="tel:+380677979385">+38 (067) 797-93-85</a>
			<a href="tel:+380632008595">+38 (063) 200-85-95</a>
			Почта: <a href="mailto:funkids@mail">funkidssodessa@gmail.com</a>
		</div>
	</li>
	</ul></nav>';
	
	echo Common::setCache($cacheFileName);
}


function route($needRoute){
	$findRoute = false;
	foreach(DI::getD('router')->routes as $route)
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
	DI::getD('request')->location(ROOT_URI . ($path ? $path . (!isset(parse_url($path)['query']) ? '/' : '') : ''));
}


/**
 *  Alias for service config->addPageType
 */
function addPageType($options){
	DI::getD('config')->addPageType($options);
}

/**
 *  Alias for service config->getPageOptionsByType
 */
function getPageOptionsByType($type){
	return DI::getD('config')->getPageOptionsByType($type);
}

function getBreadCrumbs(){
	return DI::getD('config')->getBreadCrumbs();
}


function __($key){
	static $langText;
	if(is_null($langText)){
		$langText = require_once ROOT . '/content/languages/themes/'.LANG.'.php';
	}
	return isset($langText[$key]) ? $langText[$key] : 'undefined';
}

function getOption($key){
	return Common::getOption($key);
}

function setOption($key, $value){
	Common::setOption($key, $value);
}

function langUrl($url = false){
	static $lang;
	if(is_null($lang))
		$lang = (defined('LANG') && LANG != 'ru') ? LANG . '/' : '';
	
	if($url){
		$replacement = false;
		$replaceCount = 1;
		
		if(strpos($url, SITE_URL) === 0){
			$replacement = SITE_URL;
		}elseif(strpos($url, ROOT_URI) === 0){
			$replacement = ROOT_URI;
		}
		
		if($replacement)
			$url = str_replace($replacement , $replacement  . $lang, $url, $replaceCount);
		
		return $url;
	}
	return $lang;
}

function cacheIsEnable(){
	return defined('CACHE_ON') && CACHE_ON;
}


/*filesystem*/
function do_mkdir($path, $rights = 0644){
	if(file_exists($path)){
		return false;
	}
	
	return mkdir($path, $rights) ? true : false;
}


function do_rmdir($dir) {
	if ($objs = glob($dir."/*")) {
		foreach($objs as $obj) {
			is_dir($obj) ? do_rmdir($obj) : unlink($obj);
		}
	}
	rmdir($dir);
}


function clientIp($compareIp = false){
	$ip = 	 !empty($_SERVER['HTTP_CLIENT_IP'])   	  ? $_SERVER['HTTP_CLIENT_IP'] : 
			(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
	
	return !$compareIp ? $ip : $ip == $compareIp;
}