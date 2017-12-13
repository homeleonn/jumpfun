<?php

namespace cms\controllers;

use Jump\Controller;

class PostController extends Controller{
	public function actionIndex(){
		return $this->model->single(NULL, $this->config->front_page);
	}
	
	public function actionList($category = null, $catValue = null, $tag = null, $tagValue = null, $filters = null){
		return $this->model->getPostList($category, $catValue, $tag, $tagValue, $filters);
	}
	
	public function actionSingle($url){//var_dump($url);
		return $this->model->single($url);
	}
}