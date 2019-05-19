<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\core\responce\Responce;

class TestController extends Controller{
	public function actionIndex(){
		if (isAdmin()) {
			include THEME_DIR . 'test.php';
		} else {
			(new Responce())->view('404', Responce::HTTP_NOT_FOUND);
		}
		exit;
	}
}