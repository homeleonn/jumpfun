<?php
// 'news/(new-cat)(/([a-zA-Z0-9-,=;]+[^;]))?' => 
    // array (size=2)
      // 'controller' => string 'new:list/$1/$3'

//preg_match('~^(new-cat)/([a-zA-Z0-9-]+)(/([a-zA-Z0-9-,=;]+[^;]))?/$~', 'new-cat/1/', $matches);
// preg_match('~^$~u', '', $matches);
// var_dump($matches);
// exit;
	  
define('ROOT', __DIR__ . '/');

if(!defined('ENV'))
	define('ENV', 'cms');

require_once ROOT . 'jump/bootstrap.php';

//var_dump($GLOBALS);exit;