<?php

namespace Jump\traits;

use Jump\helpers\Filter;

trait PostControllerTrait{
	private $filtersRules = [
		'page' => '/^([2-9]|\d{2,})$/',
		'view' => '/^list$/',
	];
	
	private $page = 1;
	private $perPage = 10;
	
	
	private function filtersProcessed($filters){
		global $viewParams;
		$this->filters = $filters;
		if($this->filters = Filter::analisys($filters, $this->filtersRules)){
			$fullFilters = $this->filters;
			$this->page = $this->getFilter('page', true) ?: 1;
			$viewParams['view'] = $this->getFilter('view', true) ?: 'item';
		}
		
		$this->model->setLimit($this->page, $this->perPage);
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
	
	private function textSanitize($content, $type = 'content', $tagsOn = false){
		$types = [
			'all' => [
				'from' 	=> ['<?', '<?php', '<%'],
				'to' 	=> ['']
			],
			'content' => [
				'from' 	=> [],
				'to' 	=> []
			],
			'title' => [
				'from' 	=> ['\'', '"'],
				'to' 	=> ['’', '»']
			],
		];
		if(!isset($types[$type])) $type = 'content';
		$content = str_replace($types[$type]['from'], $types[$type]['to'], str_replace($types['all']['from'], $types['all']['to'], $content));
		if(!$tagsOn)
			$content = htmlspecialchars($content);
		if($type == 'content')
			$content = html_entity_decode($content);
		
		return $content;
	}
}