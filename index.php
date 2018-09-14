<?php //if($_SERVER['REQUEST_URI'] == '/funkids/' && is_file($cacheMain = 'content/uploads/cache/pages/79.html')) {require_once $cacheMain;EXIT;}
$start = microtime(true);
define('ROOT', __DIR__ . '/');

if(!defined('ENV'))
	define('ENV', 'frontend');

require_once ROOT . 'Jump/bootstrap.php';