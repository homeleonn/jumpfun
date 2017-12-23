<?php

namespace admin\controllers;

use admin\AdminController;
use Jump\helpers\Common;

class PostController extends AdminController{
	
	private $validTerms = ['category', 'tag'];
		
	
	public function actionList(){
		return $this->model->postList();
	}
	
	public function actionTermList(){
		$data['type'] = $this->checkGettingTermType();
		return array_merge($data, ['terms' => $this->model->termList($data['type'])]);
	}
	
	public function actionAddForm(){
		if(!Common::isPage()) return $this->model->addForm();
	}
	
	public function actionAdd($type = NULL, $value = NULL){
		return !$type ? $this->model->add() : $this->model->addTermHelper($value, $type);
	}	
	
	public function actionEditForm($id){
		return $this->model->editForm($id);
	}
	
	public function actionEdit(){
		return $this->model->edit();
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