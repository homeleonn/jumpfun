<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\traits\PostControllerTrait;

class DashboardController extends Controller{
	use PostControllerTrait;
	public function actionIndex(){
		return $this->options;
	}
}