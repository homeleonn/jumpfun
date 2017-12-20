<?php

define('ROOT', __DIR__ . '/');


// $zip = new ZipArchive;
// if ($zip->open('cms.zip') === true){
	// $zip->extractTo(ROOT);
	// $zip->close();
// }else{
	// echo 'Ошибка! Архив с таким именем не задан!';
// }

if(!defined('ENV'))
	define('ENV', 'cms');

require_once ROOT . 'jump/bootstrap.php';