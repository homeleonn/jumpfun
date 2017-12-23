<?php

namespace cms\controllers;

use Jump\Controller;
use Jump\helpers\Filter;
use Jump\helpers\Pagenation;

class PostController extends Controller{
	use \Jump\traits\PostControllerTrait;
	public function actionIndex(){
		return $this->actionSingle(NULL, $this->config->front_page);
	}
	
	public function actionSingle($url, $id = NULL){
		if(!$post 		= $this->model->single($url, $id)) return 0;
		$post['meta']  	= $this->model->getMeta($post['id']);
		$post['terms'] 	= $this->model->getTermsByPostId($post['id']);
		$this->addBreadCrumbs($post);
		return $post;
	}
	
	public function actionList($taxonomy = null, $value = null, $type = null, $filters = null){
		$list = $this->options;
		$listMark = $list['type'] . 's_list';
		$this->filtersProcessed($filters);
		
		if($this->filters){
			$list[$listMark] = $this->model->getPostsByFilters($this->filters, $this->options['type']);
		}elseif(!$taxonomy){
			$list[$listMark] = $this->model->getPostsByPostType($this->options['type']);
		}else
			$list[$listMark] = $this->model->getPostList($taxonomy, $value);
		//var_dump(get_defined_vars());exit;
		if($value && isset($list[$listMark]['termName'])){
			$value = $list[$listMark]['termName'];
			unset($list[$listMark]['termName']);
		}
		$this->addBreadCrumbs($list, $taxonomy, $value, $type);
		$list['pagenation'] = (new Pagenation())->run($page, $this->model->getAllItemsCount(), $perPage, isset($fullFilters) ? Filter::stringFromFilters($fullFilters) : '');
		$list['filters'] = $this->model->getFiltersHTML($this->options);
		
		return $list;
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