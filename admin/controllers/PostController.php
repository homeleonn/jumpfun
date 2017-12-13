<?php

namespace admin\controllers;

use admin\AdminController;

class PostController extends AdminController{
	public function actionList(){
		return $this->model->postList();
	}
	
	public function actionCategoryList(){
		return $this->model->categoryList();
	}
	
	public function actionAddForm(){
		return $this->model->addForm();
	}
	
	public function actionAdd($type = NULL, $value = NULL){
		return !$type ? $this->model->add() : $this->model->addTerm($value, $type);
	}	
	
	public function actionEditForm($id){
		return $this->model->editForm($id);
	}
	
	public function actionEdit(){
		return $this->model->edit();
	}
	
	public function actionDel($id){
		$this->model->del($id);
	}
	
	
}