<?php

namespace cms\models;

use Jump\Model;
use Jump\helpers\Filter;

class Post extends Model{
	
	private $filters = [];
	private $filtersRules = [
		'page' => '/^([2-9]|\d{2,})$/',
	];
	
	public function single($url, $id = NULL){
		$post = ($id ? $this->getPostById($id) : $this->getPostByUrl($url)) ?: 0;
		
		if($post && isset($post['id']))
			$post['meta'] = $this->getMeta($post['id']);
		
		return $post;
	}
	
	public function getPostById($id){
		return $this->db->getRow('Select * from posts where id = ?i', $id);
	}
	
	public function getPostByUrl($url){
		//var_dump();exit;
		$data = $this->db->getRow('Select * from posts where url = ?s and post_type = ?s', $url, $this->postType);
		if($data){
			if($this->options['slug'])
				$this->config->addBreadCrumbs($this->options['slug'], $this->options['title']);
			$this->config->addBreadCrumbs($data['url'], $data['title']);
		}
		return $data;
	}
	
	
	public function getPostList($category, $catValue, $tag, $tagValue, $filters){//var_dump(func_get_args());exit;
	
		
		$perPage 	= 10;
		$page		= 1;
		
		if($this->filters = Filter::analisys($filters, $this->filtersRules)){
			$page = $this->getFilter('page', true) ?: 1;
		}
		
		
		//var_dump($this);
		$data = call_user_func_array([$this, 'getPostsByTerm'], $category ? [$category, $catValue, $this->filters] : [$tag, $tagValue, $this->filters]);//exit;
			
		if($data)
			$this->config->addBreadCrumbs($this->options['slug'], $this->options['title']);
		
		if($category){
			$this->config->addBreadCrumbs($category, $catValue);
		}
		
		if($tag){
			$this->config->addBreadCrumbs($tag, 'Метка: ' . $tagValue);
		}
			
		
		//var_dump($data);exit;
		//$limit = (($page - 1) * $perPage) . ', ' . $perPage;
		
		//$this->db->getAll('Select * from posts where post_type = ?s'. Filter::sql($this->filters) .' Limit ' . $limit, $this->postType);
		
		//$data = $this->di->get('config')->getCurrentPageOptions();
		
		//$data[$this->postType . '_list'] = $this->db->getAll('Select * from posts where post_type = ?s'. Filter::sql($this->filters) .' Limit ' . $limit, $this->postType);
		
		return $data;
	}
	
	public function getPostsByTerm($taxonomy, $value, $filters = false){
		
			
			//var_dump($filters);
		$data = $this->di->get('config')->getCurrentPageOptions();
		
		if(!$taxonomy){
			$data[$this->postType . 's_list'] = $this->db->getAll('Select * from posts where post_type = ?s order by id DESC', $this->postType);
		}else{
			$filtersSql = '';
			if($filters){
				foreach($filters as $key => $fvalue){
					$filtersSql .= ' and meta_key = \'' . $key . '\' and meta_value = \'' . $fvalue . '\'';
				}
			}
			
			
			//var_dump($filtersSql);
			
			
			$query = 'Select DISTINCT p.id from posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id and t.slug = ?s and tt.taxonomy = ?s';
			
			//$query = 'Select DISTINCT p.id from posts p INNER JOIN postmeta pm ON(p.id = pm.post_id) and t.slug = ?s and tt.taxonomy = ?s';
			
			
			//$query2 = 'Select post_id from postmeta where post_id IN('.$query.')' . $filtersSql;
			//var_dump( $this->db->getAll('explain Select * from posts where id IN('. $query2 .')', $value, $taxonomy));
			$data[$this->postType . 's_list'] = $this->db->getAll('Select * from posts where id IN('. $query .') order by id DESC', $value, $taxonomy);
	//	var_dump($data, 'Select * from posts where id IN('. $query2 .')', $value, $taxonomy);
		}
		
		
		return $data;
	}
	
	private function getMeta($postId){
		return $this->metaProcessing($this->db->getAll('Select meta_key, meta_value from postmeta where post_id = ?i', $postId));
	}
	
	private function metaProcessing($meta){
		if(!$meta) return false;
		
		$metaNew = [];
		
		foreach($meta as $m){
			$metaNew[$m['meta_key']] = $m['meta_value'];
		}
		
		return $metaNew;
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
	
}