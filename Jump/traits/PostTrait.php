<?php

namespace Jump\traits;

trait PostTrait{
	public function setLimit($page, $perPage){
		$this->page = (int)$page;
		if(!$this->page) $this->page = 1;
		$this->start = ($this->page - 1 ) * $perPage;
		$this->limit = ' LIMIT ' . $this->start . ',' . $perPage;
	}
	
	public function setFilters($filters, $filterAsString){
		$this->filters = $filters;
		$this->filterAsString = $filterAsString;
	}
	
	public function setOptions($options){
		$this->options = $options;
	}
	
	public function mergePostMeta($post, $mod = false){
		$meta = $this->db->getAll('Select meta_key, meta_value from postmeta where post_id = ?i', $post['id']);
		if(!$meta) return $post;
		foreach($meta as $m){
			if($mod && $m['meta_key'] == '_jmp_post_img'){
				$m['meta_value'] = UPLOADS . $this->db->getOne('select src from media where id = ' . $m['meta_value']);
			}
			$post[$m['meta_key']] = $m['meta_value'];
			if(mb_substr($m['meta_key'], 0, 1) == '_') continue;
			$post['meta_data'][$m['meta_key']] = $m['meta_value'];
		}
		return $post;
	}
	
	
	public function metaFormatting($meta){
		$newMeta = [];
		foreach($meta as $m){
			$newMeta[$m['meta_key']] = $m['meta_value'];
		}
		return $newMeta;
	}
}