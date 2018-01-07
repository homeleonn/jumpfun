<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Filter;
use Jump\helpers\Common;
use Jump\helpers\Pagenation;

class PostController extends Controller{
	use \Jump\traits\PostControllerTrait;
	
	private $taxonomyModel;
	
	/**
	 *  @param $di dependency injection container
	 *  @param object $model base model for this controller
	 */
	public function __construct($di, $model){
		parent::__construct($di, $model);
		$this->options = $this->config->getCurrentPageOptions();
		$this->model->setOptions($this->options);
	}
	
	public function actionIndex(){
		$a = $this->actionSingle(NULL, $this->config->front_page);
		return $a;
	}
	
	/**
	 *  @param string $url
	 *  @param int $id
	 *  
	 *  @return array post
	 */
	public function actionSingle($url, $id = NULL){
		$hierarchy =  func_get_args();
		
		if($url && count($hierarchy) > 1){
			foreach($hierarchy as $url){
				if(!preg_match('~^'.URL_PATTERN.'$~u', $url)){
					$this->request->location(NULL, 404);
				}
			}
			$id = NULL;
		}
		$url = array_pop($hierarchy);
		
		if(!$post = $this->model->single($url, $id)) return 0;
		$post['__model'] = $this->model;
		if($post['id'] == $this->config->front_page) return $post;
		$this->checkHierarchy($post['url'], $post['parent'], $hierarchy);
		if(!Common::isPage())
			$post['terms'] 	= $this->model->getTermsByPostId($post['id'], array_keys($this->options['taxonomy']));
		
		$this->addBreadCrumbs($post);
		
		return $post;
	}
	
	public function actionCategory(){
		$funcParams = func_get_args();
		$category = array_pop($funcParams);
		$hierarchy = $funcParams;
		var_dump($this->model->getTermsByPostId(39), $category, $hierarchy);exit;
		
	}
	
	private function checkHierarchy($url, $parent, $hierarchy){
		
		if(!$parent){
			if($hierarchy)
				$this->request->location(NULL, 404);
			return false;
		}else{
			if(!$hierarchy){
				// взять все страницы, создать иерархию и перенаправить
				$this->request->location(SITE_URL . $this->getParentHierarchy($parent) . '/' . $url . '/', 301);
			}else{
				$parents = $this->db->getAll('Select id, title, url, parent from posts where url IN(\''.implode("','", $hierarchy).'\') order by parent DESC');
				if(count($parents) < count($hierarchy)){
					$this->request->location(NULL, 404);
				}else{
					$h = array_reverse($hierarchy);
					$tempParent = $parent;
					$i = 0;
					$addBreadCrumbs = [];
					foreach($parents as $parent){
						if($parent['id'] != $tempParent || $parent['url'] != $h[$i]){
							$this->request->location(NULL, 404);
						}
						$tempParent = $parent['parent'];
						$addBreadCrumbs[$h[$i]] = $parent['title'];
						$i++;
					}
					foreach(array_reverse($addBreadCrumbs) as $link => $title){
						$this->config->addBreadCrumbs($link, $title);
					}
					
				}
			}
		}
	}
	
	private function getParentHierarchy($parentId){
		$posts = $this->db->getAll('Select id, url, parent from posts where post_type = \'page\'');
		foreach($posts as $post){
			$postsOnId[$post['id']] = $post;
		}
		
		$hierarchy = $this->setHierarchy($postsOnId, $parentId);
		$hierarchy = implode('/', array_reverse(explode('|', substr($hierarchy, 0, -1))));
		return $hierarchy;
	}
	
	private function setHierarchy($posts, $parentId){
		$hierarchy = $posts[$parentId]['url'] . '|';
		if(isset($posts[$parentId]['parent']) && $posts[$parentId]['parent']) 
			$hierarchy .= $this->setHierarchy($posts, $posts[$parentId]['parent']);
		return $hierarchy;
	}
	
	public function actionRouter($params){
		var_dump($params);exit;
	}
	
	public function actionList($taxonomy = null, $taxonomySlug = null, $page = 1){//var_dump(func_get_args());exit;
		$this->model->setLimit($this->page = $page, $this->perPage);
		$list = $this->options;
		$listMark = '__list';
		
		if(!$taxonomy){
			$list[$listMark] = $this->model->getPostsByPostType($this->options['type']);
		}else
			$list[$listMark] = $this->model->getPostList($taxonomy, $taxonomySlug);
		if(!$list[$listMark]) return 0;
		// Узнаем имя таксономии по метке для хлебных крошек
		$taxonomyName = $taxonomySlug;
		if($taxonomySlug && isset($list[$listMark]['termName'])){
			$taxonomyName = $list[$listMark]['termName'];
			unset($list[$listMark]['termName']);
		}
		$this->addBreadCrumbs($list, $taxonomy, $taxonomyName, $taxonomyName);
		$list['pagenation'] = (new Pagenation())->run($this->page, $this->model->getAllItemsCount(), $this->perPage);
		$list['filters'] = $this->model->getFiltersHTML(array_keys($this->options['taxonomy']), $this->options['type'], $this->options['slug']);
		$list['__model'] = $this->model;
		
		return $list;
	}
	
	
	/*******************/
	/*** BreadCrumbs ***/
	/*******************/
	
	private function addBreadCrumbs(&$post, $taxonomy = null, $value = null, $type = null){
		if(isset($this->options['slug']))
			$this->config->addBreadCrumbs($this->options['slug'], $this->options['title']);
		
		
		if($type){
			$this->addBreadCrumbsHelper($taxonomy, $value, $taxonomy, $post['title']);
		}elseif(isset($post['id']) && $this->config->front_page != $post['id']){
			$this->config->addBreadCrumbs($post['url'], $post['title']);
			if(isset($this->options['slug']))
				$post['title'] .= ' - ' . $this->options['title'];
		}
			
	}
	
	private function addBreadCrumbsHelper($taxonomy, $value, $text, &$postTitle){
		$this->config->addBreadCrumbs($taxonomy, $text . ': ' . $value);
		$postTitle = $value . " - {$text} " . $postTitle;
	}
}