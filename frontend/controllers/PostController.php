<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Filter;
use Jump\helpers\Common;
use Jump\helpers\Pagenation;
use frontend\models\Post\Options;

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
		Options::setOptions($this->options);
		//$this->model->setOptions($this->options);
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
	public function actionSingle($url, $id = NULL){//var_dump(func_get_args());exit;
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
		$postTypes = Options::get('type') ? [Options::get('type')] : $this->config->pageTypesWithFront;
		if(!$post = $this->model->single($url, $id, $postTypes)) return 0;
		$this->setPostOptions($post['post_type']);
		$post['__model'] = $this->model;
		if($post['id'] == $this->config->front_page) return $post;
		if(!empty($hierarchy)){
			if(!$this->options['hierarchical']){
				$this->checkTermHierarchy($post['id'], $hierarchy);
			}else{
				$this->checkHierarchy($post['url'], $post['parent'], $hierarchy);
			}
		}
			
		
		if(!$this->options['hierarchical'])
			$post['terms'] 	= $this->model->getTermsByPostId($post['id'], array_keys($this->options['taxonomy']));
		
		$this->addBreadCrumbs($post);
		
		return $post;
	}
	
	
	private function setPostOptions($postType){
		$this->options = $this->config->getPageOptionsByType($postType);
		Options::setOptions($this->options);
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
				$this->request->location(SITE_URL . $this->getParentHierarchy($this->model->getPostsByPostType('page') , $parent, 'url') . '/' . $url . '/', 301);
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
	
	private function checkTermHierarchy($postId, $hierarchy){
		$term = $this->model->getPostTerms('and p.id = ' . $postId . ' and t.slug IN(\''.implode("','", [end($hierarchy)]).'\')');
		//y, , $this->getParentHierarchy($parentId, $items)
		//foreach
		//var_dump($term, $hierarchy);exit;
		if(!$term)
			$this->request->location(null, 404);
		//var_dump($this->getParentHierarchy($term[0]['parent'], $validTerms, 'slug'));
		if(!$term[0]['parent']){
			if(count($hierarchy) > 1)
				$this->request->location(null, 404);
		}else{
			// определить валидную иерархию **-
			$validTerms = $this->model->getTaxonomies($postId);
			$urlHierarchy = implode('/', $hierarchy);
			$validUrlHierarchy = $this->getParentHierarchy($term[0]['parent'], $validTerms, 'slug') . '/' . end($hierarchy);
			//var_dump($validUrlHierarchy, $urlHierarchy);
			if($validUrlHierarchy != $urlHierarchy){
				$this->request->location(str_replace($urlHierarchy, $validUrlHierarchy , FULL_URL), 301);
			}
		}
	}
	
	private function getParentHierarchy($parentId, $items, $compare){
		foreach($items as $item){
			$itemsOnId[$item['id']] = $item;
		}
		$hierarchy = $this->setHierarchy($itemsOnId, $parentId, $compare);
		$hierarchy = implode('/', array_reverse(explode('|', substr($hierarchy, 0, -1))));
		return $hierarchy;
	}
	
	private function setHierarchy($items, $parentId, $compare){
		if(!isset($items[$parentId])) return false;
		$hierarchy = $items[$parentId][$compare] . '|';
		if(isset($items[$parentId]['parent']) && $items[$parentId]['parent']) 
			$hierarchy .= $this->setHierarchy($items, $items[$parentId]['parent']);
		return $hierarchy;
	}
	
	public function actionRouter($params){
		var_dump($params);exit;
	}
	
	public function actionList($taxonomy = null, $taxonomySlug = null, $page = 1){//var_dump(func_get_args());exit;
		$this->model->setLimit($this->page = $page, $this->options['rewrite']['paged']);
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
		
		$taxonomyTitle = $taxonomy ? $this->options['taxonomy'][$taxonomy]['title'] : '';
		$this->addBreadCrumbs($list, $taxonomyTitle, $taxonomyName, $taxonomyName);
		$list['pagenation'] = (new Pagenation())->run($this->page, $this->model->getAllItemsCount(), $this->options['rewrite']['paged']);
		$list['filters'] = $this->model->getFiltersHTML(array_keys($this->options['taxonomy']), $this->options['type'], $this->options['rewrite']['slug']);
		$list['__model'] = $this->model;
		
		return $list;
	}
	
	
	/*******************/
	/*** BreadCrumbs ***/
	/*******************/
	
	private function addBreadCrumbs(&$post, $taxonomyTitle = null, $value = null, $type = null){
		if(isset($this->options['rewrite']['slug']) && !Options::front())
			$this->config->addBreadCrumbs($this->options['rewrite']['slug'], $this->options['title']);
		
		
		if($type){
			$this->addBreadCrumbsHelper($taxonomyTitle, $value, $taxonomyTitle, $post['title']);
		}elseif(isset($post['id']) && $this->config->front_page != $post['id']){
			$this->config->addBreadCrumbs($post['url'], $post['title']);
			if(isset($this->options['rewrite']['slug']))
				$post['title'] .= ' - ' . $this->options['title'];
		}
			
	}
	
	private function addBreadCrumbsHelper($taxonomyTitle, $value, $text, &$postTitle){
		$this->config->addBreadCrumbs($taxonomyTitle, $text . ': ' . $value);
		$postTitle = $value . " - {$text} " . $postTitle;
	}
}