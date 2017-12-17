<?php

namespace Jump\helpers;
use Jump\helpers\Filter;

trait CurrentWork{
	private function getPostsByFilters($filters){
		//$filtersFullOriginal = $filters;
		$sqlFilters = '';
		// Соберем таксономии и проверим их валидность
		// Если найдутся невалидные, нужно вырезать из запроса данный фильтр и перенаправить
		// Если валидных меньше чем присланных, проходимся по валидным, сверяемся с присланными, сохраняем невалидные присланные, формируем и режем
		$validFilters = $this->taxonomyValidation(array_keys($filters));
		if(count($filters) > count($validFilters)){
			// Удалим валидные из присланных, те что останутся - вырезаются
			foreach($validFilters as $filter){
				$taxonomy = str_replace($this->options['type'] . '-', '', $filter['taxonomy']);
				if(isset($filters[$taxonomy])){
					unset($filters[$taxonomy]);
				}
			}
			$this->cleaningInvalidFiltersAndRelocation(Filter::stringFromValidFilters($this->filters), Filter::stringFromValidFilters($filters));
		}
		//var_dump($filters, $validFilters);exit;
		
		//var_dump($filters, $validFilters);exit;
		
		foreach($filters as $taxonomy => $slugs){
			$slugs = str_replace(',', '\' OR t.slug = \'', $slugs);
			$sqlFilters .= " (tt.taxonomy = '{$this->options['type']}-{$taxonomy}' and (t.slug = '" . $slugs . "')) OR";
		}
		
		$sqlFilters = substr($sqlFilters, 0, -3);
		//var_dump($sqlFilters, $filters);exit;
		
		//var_dump($sqlFilters);
		$data[$this->options['type'] . 's_list'] = $this->db->getAll('Select * from posts where id IN(Select DISTINCT p.id from posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id and '.$sqlFilters.') order by id DESC');
			
		
		
		
		
		//var_dump($data);exit;
			
		
		return $data;
	}
	
	private function taxonomyValidation($taxonomies){
		$type = $this->options['type'] . '-';
		return $this->db->getAll('Select DISTINCT taxonomy from term_taxonomy where taxonomy = \'' . $type . implode('\' OR taxonomy = \'' . $type, $taxonomies) . '\'');
	}
	
	private function cleaningInvalidFiltersAndRelocation($fullFilters, $invalidFilters){
		$copyFullFilters = $fullFilters;
		if(strcmp($fullFilters, $invalidFilters) === 0){
			$url = str_replace($fullFilters . '/', '', FULL_URL);
		}else{
			foreach(explode(';', $invalidFilters) as $filter){
				$len = mb_strlen($fullFilters);
				$fullFilters = str_replace($filter.';', '', $fullFilters);
				if(mb_strlen($fullFilters) == $len) $fullFilters = str_replace(';'.$filter, '', $fullFilters);
				if(mb_strlen($fullFilters) == $len) $fullFilters = str_replace($filter, '', $fullFilters);
			}
			$url = str_replace($copyFullFilters, $fullFilters, FULL_URL);
		}
		$this->request->location($url);
	}
}