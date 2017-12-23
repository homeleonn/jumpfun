<?php

namespace Jump\traits;

trait PostTrait{
	public function setLimit($page, $perPage){
		$this->page = (int)$page;
		$this->start = ($this->page - 1 ) * $perPage;
		$this->limit = ' LIMIT ' . $this->start . ',' . $perPage;
	}
	
	public function setFilters($filters, $filterAsString){
		$this->filters = $filters;
		$this->filterAsString = $filterAsString;
	}
}