<?php

namespace admin\controllers;

use Jump\Controller;

class UserController extends Controller{
	public function actionIndex(){
		dd(123);
	}
	
	public function actionList(){
		\Responce::notFound();
	}
}

