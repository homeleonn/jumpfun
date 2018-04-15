<?php

namespace admin\controllers;

use admin\AdminController;
use Jump\traits\PostControllerTrait;
use Jump\helpers\Common;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Transliteration;
use frontend\models\Post\Options;
use Jump\core\responce\Responce;

class PostController extends AdminController{
	use PostControllerTrait;
	
	private $imgUploader;
	
	public function __construct(){
		parent::__construct();
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
	
	public function actionAdd(){//dd($this->request->post, $_FILES);exit;
		list($post, $extraFields) = $this->postProcessing($this->request->post['title']);
		$post['url'] = $this->model->checkUrl($post['url'], $post['parent']);
		$post['post_type'] = $this->options['type'];
		$result = $this->model->add($post, $extraFields);
		return $result;
	}	
	
	public function actionEditForm($id){
		$post = $this->model->editForm($id);
		if(!$post) return 0;
		$key = '_jmp_post_img';
		$post[$key] = $this->getPostImg($post, $key);
		$post['comments'] = $this->model->getComments($id);
		$post['__model'] = $this->model;
		return $post;
	}
	
	private function getPostImg($post, $key){
		if(isset($post[$key])){
			return $this->db->getRow('Select * from media where id = ?i', (int)$post[$key]);
			if(!$post[$key]){
				$this->db->query('Delete from postmeta where post_id = ?i and meta_key = ?s', $post['id'], $key);
			}
		}
	}
	
	public function actionEdit(){//var_dump($this->request->post);//exit;
		list($post, $extraFields) = $this->postProcessing($this->request->post['url']);
		if($this->model->checkUrlExists($post['url'], $post['parent'], $post['id'])) Msg::json('Введенный адрес уже существует!', 0);
		$result = $this->model->edit($post, $extraFields);
		Msg::json(1);
	}
	
	
	
	private function postProcessing($urlStringForTranslit){
		if($this->request->post['title'] == '') Msg::json('Заголовок не должен быть пуст!', 0);
		$post = [
			'parent' 		=> isset($this->request->post['parent']) ? (int)$this->request->post['parent'] : 0,
			'title' 		=> $this->textSanitize($this->request->post['title'], 'title'),
			'url' 			=> Transliteration::run($urlStringForTranslit),
			'content' 		=> $this->textSanitize($this->request->post['content']),
			'modified' 		=> MyDate::getDateTime(),
			'comment_status' => isset($this->request->post['discussion']) ? 'open' : 'closed',
			'id' 			=> isset($this->request->post['id']) ? (int)$this->request->post['id'] : 0,
		];
		if(!$this->model->checkExistsPostById($post['parent']))
			Msg::json('Данного родителя не существует', 0);
		$extraFields = isset($this->request->post['extra_fileds']) ? $this->request->post['extra_fileds'] : [];
		
		//fill extra fields
		$extraFieldKeys = [];
		$extraFieldKeys = \applyFilter('extra_fields_keys', $extraFieldKeys);
		
		if(!$extraFieldKeys || !is_array($extraFieldKeys)) $extraFieldKeys = [];
		$extraFieldKeys = array_merge($extraFieldKeys, [
			'_jmp_post_template', 
			'_jmp_post_img',
		]);
		
		foreach($extraFieldKeys as $key){
			if(isset($this->request->post[$key]) && $this->request->post[$key]){
				$extraFields[$key] = $this->request->post[$key];
			}
		}
		
		return [$post, $extraFields];
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
	
	public function actionCommentsList(){
		$comments 			= $this->db->getAll('Select * from comments limit 20');
		$ids 				= Common::getKeys($comments, 'comment_post_id', true);
		$posts 				= $this->db->getAll('Select id, title, url, post_type, parent from posts where id IN('.implode(',', $ids).')');
		$postsOnTypes 		= Common::itemsOnKeys($posts, ['post_type']);
		$currentPostTypes 	= array_keys($postsOnTypes);
		$pageTypes 			= $this->config->getOption('jump_pageTypes');
		
		$taxonomies = [];
		foreach($currentPostTypes as $cpt){
			$taxonomies = array_merge($taxonomies, array_keys($pageTypes[$cpt]['taxonomy']));
		}
		$taxonomies = $this->model->taxonomy->getByTaxonomies($taxonomies);
		
		list($termsOnId, $termsOnParent) = Common::itemsOnKeys($taxonomies, ['id', 'parent']);
		$termsByPostIds = $this->model->getTermsByPostsId($ids);
		
		
		$postsOnId = [];
		foreach($posts as $item){
			$termsByPostId = isset($termsByPostIds[$item['id']]) ? $termsByPostIds[$item['id']] : NULL;
			$permalink 	 = SITE_URL . trim($pageTypes[$item['post_type']]['rewrite']['slug'], '/') . '/' . $item['url'] . '/';
			$item['url'] = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
			$postsOnId[$item['id']] = $item;
		}
		
		$this->view->render('comments/list', ['comments' => $comments, 'posts_on_id' => $postsOnId]);
	}
	
	public function postsLinkCreate($posts){
		dd();
	}
}