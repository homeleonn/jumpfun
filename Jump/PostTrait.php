<?php

namespace Jump;

trait PostTrait{
	
	public function single($url, $id = NULL){
		return ($id ? $this->getPostById($id) : $this->getPostByUrl($url)) ?: 0;
	}
	
	public function getPostById($id){
		return $this->db->getRow('Select * from posts where id = ?i', $id);
	}
	
	public function getPostByUrl($url){
		return $this->db->getRow('Select * from posts where url = ?s and post_type = ?s', $url, $this->options['type']);
	}
	
	public function getPostList($page){
		$perPage = 10;
		
		$limit = (($page - 1) * $perPage) . ', ' . $perPage;
		//var_dump($limit, $page);
		
		$data[$this->options['type'] . '_list'] = $this->db->getAll('Select * from posts where post_type = ?s Limit ' . $limit, $this->options['type']);
		
		return $data;
	}
}