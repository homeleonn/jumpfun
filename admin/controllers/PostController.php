<?php

namespace admin\controllers;

use admin\AdminController;
use Jump\traits\PostControllerTrait;
use Jump\helpers\Common;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Transliteration;
use frontend\models\Post\Options;

class PostController extends AdminController{
	use PostControllerTrait;
	
	private $imgUploader;
	
	public function __construct($di, $model){
		parent::__construct($di, $model);
		$this->setOptions();
	}
	
	private function setOptions(){
		$this->options = $this->model->options = $this->config->getCurrentPageOptions();
		Options::setOptions($this->options);
	}
	
	public function actionList(){
		return $this->model->postList();
	}
	
	public function actionTermList(){
		$data['term'] = $_GET['term'];
		if(!isset($this->options['taxonomy']) || !in_array($data['term'], array_keys($this->options['taxonomy']))) return 0;
		return ['term' => $data['term'], 'terms' => $this->model->termList($data['term'])];
	}
	
	public function actionAddForm(){
		return $this->model->addForm();
	}
	
	public function actionAdd(){//var_dump($this->request->post, $_FILES);exit;
		if($this->request->post['title'] == '') Msg::json('Заголовок не должен быть пуст!', 0);
		list($title, $url, $content, $parent, $modified, $extraFields) = $this->postProcessing($this->request->post['title']);
		$url 		= $this->model->checkUrl($url, $parent);
		$posType	= $this->options['type'];
		$result = $this->model->add($title, $url, $content, $parent, $posType, $extraFields);
		return $result;
	}	
	
	public function actionEditForm($id){
		$data = $this->model->editForm($id);
		if(isset($data['_jmp_post_img'])){
			$data['_jmp_post_img'] = $this->db->getRow('Select * from media where id = ?i', (int)$data['_jmp_post_img']);
			if(!$data['_jmp_post_img']){
				$this->db->query('Delete from postmeta where post_id = ?i and meta_key = ?s', $id, '_jmp_post_img');
			}
		}
			
		$data['__model'] = $this->model;
		return $data;
	}
	
	public function actionEdit(){//var_dump($this->request->post);//exit;
		if($this->request->post['title'] == '') exit;
		$id = (int)$this->request->post['id'];
		list($title, $url, $content, $parent, $modified, $extraFields) = $this->postProcessing($this->request->post['url']);
		if($this->model->checkUrlExists($url, $parent, $id)) Msg::json('Введенный адрес уже существует!', 0);
		
		$result = $this->model->edit($title, $url, $content, $parent, $modified, $id, $extraFields);
		Msg::json(1);
	}
	
	private function postProcessing($urlStringForTranslit){
		$parent = isset($this->request->post['parent']) ? (int)$this->request->post['parent'] : 0;
		if(!$this->model->checkExistsPostById($parent))
			Msg::json('Данного родителя не существует', 0);
		
		$title 		= $this->textSanitize($this->request->post['title'], 'title'); 
		$url 		= Transliteration::run($urlStringForTranslit);
		$content 	= $this->textSanitize($this->request->post['content']); 
		$modified 	= MyDate::getDateTime();
		$extraFields = isset($this->request->post['extra_fileds']) ? $this->request->post['extra_fileds'] : [];
		
		
		//fill extra fields
		$extraFieldKeys = [
			'_jmp_post_template', 
			'_jmp_post_img',
		];
		foreach($extraFieldKeys as $key){
			if(isset($this->request->post[$key]) && $this->request->post[$key]){
				$extraFields[$key] = $this->request->post[$key];
			}
		}
		
		return [$title, $url, $content, $parent, $modified, $extraFields];
		
	}
	
	public function actionDel($id, $type){
		$this->model->del($id, $type);
	}
	
	
	
	// TERMS
	public function actionAddTermForm(){
		$this->checkGettingTermType();
		return $this->model->addTermForm($this->request->get['term']);
	}
	
	public function actionAddTerm(){
		$async = isset($this->request->post['async']) ? $this->request->post['async'] : false;
		
		if(!$name = $this->request->post['name']){
			if(!$async)
				$this->request->location(FULL_URL);
			else
				exit;
		}
		
		if($async){
			$slug 	 = $description = $parent = '';
			$whisper = false;
		}else{
			$slug 		 = $this->request->post['slug'];
			$description = $this->request->post['description'];
			$parent 	 = $this->request->post['parent'];
			$whisper 	 = true;
		}
		
		$result = $this->model->addTerm($name, $this->request->post['term'], $whisper, $slug, $description, $parent);
		
		if($result && $async){
			exit('1');
		}
			
		$url = $result ? (SITE_URL . 'admin/' . $this->options['type'] . '/terms/?term=' . $this->request->post['term']) : (FULL_URL . '&msg=Термин уже существует');
		
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
			$this->request->post['description'],
			$this->request->post['parent']
		);
	}
	
	
	private function checkGettingTermType(){
		if(!isset($this->request->get['term']) || !$this->checkValidTerms($this->request->get['term'])){
			$this->goToPostTypePage();
		}
		return $this->request->get['term'];
	}
	
	
	private function goToPostTypePage(){
		$this->request->location(SITE_URL . 'admin/' . $this->options['type'] . '/');
	}
	
	private function checkValidTerms($term){
		return isset($this->options['taxonomy']) ? in_array($term, array_keys($this->options['taxonomy'])) : false;
	}
	
	
}