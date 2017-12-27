<?php

namespace cms\controllers;

use Jump\Controller;
use Jump\helpers\Filter;
use Jump\helpers\Common;
use Jump\helpers\Pagenation;

class PostController extends Controller{
	use \Jump\traits\PostControllerTrait;
	public function actionIndex(){
		return $this->actionSingle(NULL, $this->config->front_page);
	}
	
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
		
		$this->checkHierarchy($post['url'], $post['parent'], $hierarchy);
		//var_dump($url, $hierarchy, $id);exit;
		//$post['meta']  	= $this->model->getMeta($post['id']);
		if(!Common::isPage())
			$post['terms'] 	= $this->model->getTermsByPostId($post['id']);
		$this->addBreadCrumbs($post);
		return $post;
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
				$parents = $this->db->getAll('Select id, url, parent from posts where url IN(\''.implode("','", $hierarchy).'\') order by parent DESC');
				if(count($parents) < count($hierarchy)){
					$this->request->location(NULL, 404);
				}else{
					$h = array_reverse($hierarchy);
					$tempParent = $parent;
					$i = 0;
					foreach($parents as $parent){
						if($parent['id'] != $tempParent || $parent['url'] != $h[$i++]){
							$this->request->location(NULL, 404);
						}
						$tempParent = $parent['parent'];
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
		
		// $parents[] = $postsOnId[$parentId];
		// $hierarchy = $postsOnId[$parentId]['url'] . '|';
		// $i = 0;
		// while($parents[$i]){
			// if(!$parents[$i]['parent']) break;
			// if(isset($postsOnId[$parents[$i]['parent']])){
				// $parents[] = $postsOnId[$parents[$i]['parent']];
				// $hierarchy .= $postsOnId[$parents[$i]['parent']]['url'] . '|';
			// }
			// $i++;
		// }
		
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