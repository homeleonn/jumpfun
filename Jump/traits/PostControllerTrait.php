<?php

namespace Jump\traits;

use Jump\helpers\Filter;

trait PostControllerTrait{
	private $filtersRules = [
		'page' => '/^([2-9]|\d{2,})$/',
		'view' => '/^list$/',
	];
	
	
	private function filtersProcessed($filters){
		global $viewParams;
		$this->filters = $filters;
		$page = 1;
		$perPage = 10;
		if($this->filters = Filter::analisys($filters, $this->filtersRules)){
			$fullFilters = $this->filters;
			$page = $this->getFilter('page', true) ?: 1;
			$viewParams['view'] = $this->getFilter('view', true) ?: 'item';
		}
		
		$this->model->setLimit($page, $perPage);
		$this->model->setFilters($this->filters, $filters);
		
		return isset($fullFilters) ? $fullFilters : [];
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