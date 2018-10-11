<?php
ini_set('xdebug.var_display_max_depth', 50);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
ini_set('date.timezone', 'Europe/Kiev');
ini_set('xdebug.overload_var_dump', '1');

define('DS', DIRECTORY_SEPARATOR);

define('ROOT_URI', (substr($_SERVER['DOCUMENT_ROOT'], -1) == '/' ? '/' : '') . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', ROOT)) ?: '/');

if($_SERVER['PHP_SELF'] == '/index.php'){
	$fullUri = $_SERVER['REQUEST_URI'] == '/' ? '' : substr($_SERVER['REQUEST_URI'], 1);
}else{
	$fullUri = $_SERVER['REQUEST_URI'] == ROOT_URI ? '/' : (ROOT_URI == '/' ? substr($_SERVER['REQUEST_URI'], 1) : str_replace(ROOT_URI, '', $_SERVER['REQUEST_URI']));
}

if(strpos($fullUri, 'en/') === 0) {
	if($fullUri == 'en/') $fullUri = '/';
	else{
		$replaceCount = 1;
		$fullUri = str_replace('en/', '', $fullUri, $replaceCount);
	}
	define('LANG', 'en');
}else
	define('LANG', 'ru');


define('FULL_URI', $fullUri != '' ? $fullUri : '/');

define('URI', explode('?', FULL_URI)[0]);

define('SITE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . ROOT_URI);
define('FULL_URL', SITE_URL . (FULL_URI != '/' ? FULL_URI : ''));
define('FULL_URL_WITHOUT_PARAMS', SITE_URL . (URI != '/' ? URI : ''));


define('THEME', SITE_URL . 'content/themes/funkids/');
define('THEME_DIR', ROOT . 'content/themes/funkids/');

define('ADMIN_THEME', SITE_URL . 'admin/view/');
define('UPLOADS_DIR', ROOT . 'content/uploads/');
define('UPLOADS', SITE_URL . 'content/uploads/');
define('PLUGINS', SITE_URL . 'content/plugins/');
define('CACHE_DIR', ROOT . 'content/uploads/cache/');

define('URL_PATTERN', '[а-яА-ЯЁa-zA-Z0-9-]+');
define('URL_PATTERN_SLASH', '[а-яА-ЯЁa-zA-Z0-9-\/]+');
define('FILTER_PATTERN', '[^;\-,=][a-zA-Z0-9-,=;]+[^;,=]');

define('S', '|'); //sumbol separator for args parse

define('TEMPLATE', '/^[ \t\/*#@]*Template:(.*)$/mi');
define('PLUGIN', '/^[ \t\/*#@]*needle:(.*)$/mi');

define('CACHE_ON', FALSE);

//echo '<pre>';var_dump($_SERVER, $_SESSION, ROOT_URI, FULL_URI, URI, SITE_URL, FULL_URL_WITHOUT_PARAMS, FULL_URL);exit;