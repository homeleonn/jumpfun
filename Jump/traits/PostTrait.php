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
	
	public function mergePostMeta($post){
		$meta = $this->db->getAll('Select meta_key, meta_value from postmeta where post_id = ?i', $post['id']);
		if(!$meta) return $post;
		$post['meta_data'] = $this->metaFormatting($meta);
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