<?php

namespace cms\controllers;

use Jump\Controller;
use Jump\helpers\Filter;
use Jump\helpers\Pagenation;

class PostController extends Controller{
	public function actionIndex(){
		return $this->actionSingle(NULL, $this->config->front_page);
	}
	
	public function actionSingle($url, $id = NULL){
		if(!$post = $this->model->single($url, $id)) return 0;
		if(isset($post['id'])) 
			$post['meta'] = $this->model->getMeta($post['id']);
		$this->addBreadCrumbs($post);
		return $post;
	}
	
	public function actionList($taxonomy = null, $value = null, $type = null, $filters = null){
		$this->filters = $filters;
		global $viewParams;
		$list 	  = $this->options;
		$listMark = $list['type'] . 's_list';
		$page = 1;
		$perPage = 1;
		if($this->filters = Filter::analisys($filters, $this->filtersRules)){
			$fullFilters = $this->filters;
			$page = $this->getFilter('page', true) ?: 1;
			$viewParams['view'] = $this->getFilter('view', true) ?: 'item';
		}
		
		$this->model->setLimit($page, $perPage);
		$this->model->setFilters($this->filters);
		
		if($this->filters){
			$list[$listMark] = $this->model->getPostsByFilters($this->filters, $this->options['type']);
		}elseif(!$taxonomy){
			$list[$listMark] = $this->model->getPostsByPostType();
		}else
			$list[$listMark] = $this->model->getPostList($taxonomy, $value);
		
		$this->addBreadCrumbs($list, $taxonomy, $value, $type);
		//var_dump($page, $this->model->getAllItemsCount(), $perPage, isset($fullFilters) ? Filter::stringFromFilters($fullFilters) : '');
		$list['pagenation'] = (new Pagenation())->run($page, $this->model->getAllItemsCount(), $perPage, isset($fullFilters) ? Filter::stringFromFilters($fullFilters) : '');
		
		return $list;
	}
	
	private function getFilter($name, $delete = false){
		$filterNecessary = false;
		
		if(isset($this->filters[$name])){
			$filterNecessary = $this->filters[$name];
			if($delete)
				unset($this->filters[$name]);
		}
		
		return $filterNecessary;
	}
	
	
	/*******************/
	/*** BreadCrumbs ***/
	/*******************/
	
	private function addBreadCrumbs(&$post, $taxonomy = null, $value = null, $type = null){
		if(isset($this->options['slug']))
			$this->config->addBreadCrumbs($this->options['slug'], $this->options['title']);
		
		if($type){
			$this->addBreadCrumbsHelper($taxonomy, $value, ($type == 'category' ? 'категория' : 'метка'), $post['title']);
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