<?php

namespace admin\controllers;

use admin\AdminController;

class CategoryController extends AdminController{
	public function actionList(){
		$data = $this->db->getAll('Select * from categories');
		//$this->view->render('post/list', $data);exit;
		//var_dump($data);
		return $data;
	}
}