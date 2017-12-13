<?php

define('DS', DIRECTORY_SEPARATOR);

define('ROOT_URI', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', ROOT)) ?: '/');

if($_SERVER['PHP_SELF'] == '/index.php'){
	define('FULL_URI', $_SERVER['REQUEST_URI'][0] != '/' ? $_SERVER['REQUEST_URI'] : substr($_SERVER['REQUEST_URI'], 1));
}else{
	define('FULL_URI', $_SERVER['REQUEST_URI'] != ROOT_URI ? str_replace(ROOT_URI, '', $_SERVER['REQUEST_URI']) : '/');
}

define('URI', explode('?', FULL_URI)[0]);

define('SITE_URL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . ROOT_URI);
define('FULL_URL', SITE_URL . FULL_URI);


define('THEME', SITE_URL . 'content/themes/default/');
define('THEME_DIR', ROOT . 'content/themes/default/');

define('ADMIN_THEME', SITE_URL . 'admin/view/');

define('URL_PATTERN', '[а-яА-ЯЁa-zA-Z0-9-]+');


//var_dump(ROOT_URI, FULL_URI, URI, SITE_URL, FULL_URL);exit;