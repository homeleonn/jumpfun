<?php

namespace cms\controllers;

use Jump\Controller;
use cms\models\Login;

class UserController extends Controller{
	public function actionAuth(){
		var_dump(__METHOD__);
	}
	
	public function actionLogin(){
		return true;
	}
}