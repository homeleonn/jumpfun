<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\traits\PostControllerTrait;

class DashboardController extends Controller{
	use PostControllerTrait;
	public function actionIndex(){
		if(!isset($_GET['page']))
			return false;
		
		$page = htmlspecialchars($_GET['page']);
		
		ob_start();
		doAction('admin_page', $page);
		return ['content' => ob_get_clean()];
	}
	
	public function actionSave(){
		if(!empty($_POST)){
			foreach($_POST as $k => $v){
				setOption($k, serialize($v));
			}
		}
		redirect('admin' . (isset($_GET['page']) ? '/?page=' . $_GET['page'] : ''));
	}
}