<?php
$start = microtime(true);
define('ROOT', __DIR__ . '/');

if(!defined('ENV'))
	define('ENV', 'cms');

require_once ROOT . 'jump/bootstrap.php';