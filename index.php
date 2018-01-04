<?php
$start = microtime(true);
define('ROOT', __DIR__ . '/');

if(!defined('ENV'))
	define('ENV', 'frontend');

require_once ROOT . 'Jump/bootstrap.php';