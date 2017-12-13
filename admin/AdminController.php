<?php

namespace admin;

use Jump\Controller;

class AdminController extends Controller{
	
	static $postType;
	static $modelName = 'post';
	
	public function __construct($di, $model){
		self::$postType = substr(URI, 6);
		self::$postType = substr(self::$postType, 0, strpos(self::$postType, '/'));
		//var_dump(self::$postType);exit;
		parent::__construct($di, $model);
	}
}