<?php
if(empty($_GET['files'])) exit;

header('content-type text/javascript charset=utf-8;');
$jsFileNames = explode(',', $_GET['files']);
foreach($jsFileNames as $file){
	$filename = "../view/js/{$file}.js";
	//var_dump($filename);
	if(file_exists($filename))
		require_once $filename;
}