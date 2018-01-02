<?php

namespace admin\controllers;

use admin\AdminController;
use Jump\traits\PostControllerTrait;
use Jump\helpers\Common;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Transliteration;

class PostController extends AdminController{
	use PostControllerTrait;
	private $validTerms = ['category', 'tag'];
		
	
	public function actionList(){
		return $this->model->postList();
	}
	
	public function actionTermList(){
		$data['type'] = $this->checkGettingTermType();
		return array_merge($data, ['terms' => $this->model->termList($data['type'])]);
	}
	
	public function actionAddForm(){
		return $this->model->addForm();
	}
	
	public function actionAdd($type = NULL, $value = NULL){
		if(!$type){
			if($this->request->post['title'] == '') exit;
			list($title, $url, $content, $parent, $modified) = $this->postProcessing($this->request->post['title']);
			$url 		= $this->model->checkUrl($url);
			$posType	= $this->options['type'];
			$data = $this->model->add($title, $url, $content, $parent, $posType);
		}else{
			$data = $this->model->addTermHelper($value, $type);
		}
		return $data;
	}	
	
	public function actionEditForm($id){
		return $this->model->editForm($id);
	}
	
	public function actionEdit(){
		if($this->request->post['title'] == '') exit;
		if($this->model->checkUrlExists($this->request->post['url'], $this->request->post['id'])) Msg::json('Введенный адрес уже существует!', 0);
		
		$id = (int)$this->request->post['id'];
		list($title, $url, $content, $parent, $modified) = $this->postProcessing($this->request->post['url']);
		return $this->model->edit($title, $url, $content, $parent, $modified, $id);
	}
	
	private function postProcessing($transitString){
		$parent 	= (int)$this->request->post['parent'];
		if(!$this->model->checkExistsPostById($parent))
			Msg::json('Данного родителя не существует', 0);
		
		$title 		= $this->textSanitize($this->request->post['title'], 'title'); 
		$url 		= Transliteration::run($transitString);
		$content 	= $this->textSanitize($this->request->post['content']); 
		$modified 	= MyDate::getDateTime();
		return [$title, $url, $content, $parent, $modified];
	}
	
	public function actionDel($id, $type){
		$this->model->del($id, $type);
	}
	
	
	
	// TERMS
	public function actionAddTermForm(){
		$this->checkGettingTermType();
		return $this->model->addTermForm($this->request->get['type']);
	}
	
	public function actionAddTerm(){
		$async = isset($this->request->post['async']) ? $this->request->post['async'] : false;
		
		if(!$this->request->post['name']){
			if(!$async)
				$this->request->location(FULL_URL);
			else
				exit;
		}
		
		$whisper = true;
		
		if($async){
			$whisper = false;
			$this->request->post['slug'] = '';
			$this->request->post['description'] = '';
		}
		
		$result = $this->model->addTerm($this->request->post['name'], $this->request->post['type'], $whisper, $this->request->post['slug'], $this->request->post['description']);
		
		if($result && $async){
			exit('1');
		}
			
		
		$url = $result ? (SITE_URL . 'admin/' . $this->options['slug'] . '/edit-term/' . $this->db->insertId() . '/') : (FULL_URL . '&msg=Термин уже существует');
		
		$this->request->location($url);
	}
	
	public function actionEditTermForm($id){
		return $this->model->editTermForm($id);
	}
	
	public function actionEditTerm(){
		return $this->model->editTerm(
			$this->request->post['id'],
			$this->request->post['name'],
			$this->request->post['slug'],
			$this->request->post['description']
		);
	}
	
	
	private function checkGettingTermType(){
		if(!isset($this->request->get['type']) || !$this->checkValidTerms($this->request->get['type'])){
			$this->goToPostTypePage();
		}
		return $this->request->get['type'];
	}
	
	
	private function goToPostTypePage(){
		$this->request->location(SITE_URL . 'admin/' . $this->options['slug'] . '/');
	}
	
	private function checkValidTerms($term){
		return in_array($term, $this->validTerms);
	}
	
	
}