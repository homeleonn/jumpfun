<?php

namespace frontend\controllers;

use Jump\Controller;
use frontend\models\Login;

class UserController extends Controller{
	public function actionAuth(){
		var_dump(__METHOD__);
	}
	
	public function actionLogin(){
		$data['title'] = 'Авторизация';
		return $data;
	}
}