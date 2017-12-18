<?php

namespace Jump\helpers;
use Jump\helpers\Filter;

trait CurrentWork{
	private function getPostsByFilters($filters, $postType){
		$sqlFilters = '';
		// Соберем таксономии и проверим их валидность
		// Если найдутся невалидные, нужно вырезать из запроса данный фильтр и перенаправить
		// Если валидных меньше чем присланных, проходимся по валидным, сверяемся с присланными, сохраняем невалидные присланные, формируем и режем
		$validFilters = $this->taxonomyValidation($filters, $postType);
		
		$filtersAsString = Filter::stringFromFilters($filters);
		$validFiltersAsString = Filter::stringFromFilters($validFilters);
	
		//var_dump($filters, $validFilters, $filtersAsString, $validFiltersAsString);
		
		if(strcmp($filtersAsString, $validFiltersAsString) !== 0){
			$this->request->location(str_replace($filtersAsString . (!$validFiltersAsString ? '/' : ''), $validFiltersAsString, FULL_URL), 301);
		}
		
		// Формируем условие из валидных фильтров
		foreach($validFilters as $taxonomy => $slugs){
			$sqlFilters .= " (tt.taxonomy = '{$postType}-{$taxonomy}' and t.slug IN('" . str_replace(',', "','", $slugs) . "')) OR";
		}
		$q = 'Select * from posts where id IN(Select DISTINCT p.id from posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id and ('.substr($sqlFilters, 0, -3).')) order by id DESC';
		
		$data = $this->db->getAll($q);
		//var_dump($q, $data);exit;
		return $data;
	}
	
	// Поиск валидных фильтров и их значений из присланных
	private function taxonomyValidation($filters, $postType){
		$type = $this->options['type'] . '-';
		$taxonomies = '';
		
		// Формируем условие из фильтров и их значений
		// Узнаем ваилидные фильтры, и валидные значения
		foreach($filters as $taxonomy => $values){
			$taxonomies .= "(tt.taxonomy = '{$type}{$taxonomy}' AND t.slug IN('" . str_replace(',', "','", $values) . "')) OR ";
		}
		
		$validTaxonomies = $this->db->getAll('Select DISTINCT tt.taxonomy as filter, t.slug as value from term_taxonomy as tt, terms as t where tt.term_taxonomy_id = t.id and ' . substr($taxonomies, 0, -3));
		
		return $this->creatingValidFilters($validTaxonomies, $postType);
	}
	
	// Форматирование новых валидных фильтров и их значеий
	private function creatingValidFilters($validFilters, $postType){
		$newValidFilters = [];
		
		foreach($validFilters as $filter){
			$newValidFilters[str_replace($postType . '-', '', $filter['filter'])][] = $filter['value'];
		}
		
		foreach($newValidFilters as $filters => $values){
			$newValidFilters[$filters] = implode(',', $values);
		}
		
		return $newValidFilters;
	}
}