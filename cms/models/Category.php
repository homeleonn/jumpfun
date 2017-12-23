<?php

namespace cms\models;

use Jump\Model;
use Jump\helpers\Filter;

class Category extends Model{
	use \Jump\traits\PostTrait;
	
	public function getSingleCategory($id, $url){
		return $this->db->getRow('Select * from categories where id = ?i and url = ?s', $id, $url);
	}
	
	public function getCategoriesByParent($parentId){
		return $this->db->getRow('Select * from categories where parent = ?i', $parentId);
	}
	
	
	public function getProductsByFilters($filters, $catId){
		$sqlFilters = '';
		// Соберем таксономии и проверим их валидность
		// Если найдутся невалидные, нужно вырезать из запроса данный фильтр и перенаправить
		// Если валидных меньше чем присланных, проходимся по валидным, сверяемся с присланными, сохраняем невалидные присланные, формируем и режем
		
		$validFilters = $this->taxonomyValidation($filters, $catId);
		
		$filtersAsString = Filter::stringFromFilters($filters);
		$validFiltersAsString = Filter::stringFromFilters($validFilters);
	
		//var_dump($filters, $validFilters, $filtersAsString, $validFiltersAsString);exit;
		
		if(strcmp($filtersAsString, $validFiltersAsString) !== 0){var_dump(1);exit;
			$this->request->location(str_replace($filtersAsString . (!$validFiltersAsString ? '/' : ''), $validFiltersAsString, FULL_URL), 301);
		}
		
		// Формируем условие из валидных фильтров
		foreach($validFilters as $filterGroup => $slugs){
			
			$sqlFilters .= " (fg.group_slug = '{$filterGroup}' AND (f.slug = '" . str_replace(',', "' OR f.slug = '", $slugs) . "')) AND ";
		}
		var_dump($sqlFilters);exit;
		$query = 'SELECT p.* from products as p where id IN(Select DISTINCT p.id from products as p, filters as f, filter_group as fg, relationships as r where p.cat_id = fg.cat_id and f.group_id = fg.group_id and f.id = r.filter_id and r.product_id = p.id and ('.substr($sqlFilters, 0, -4).')) order by id DESC';
		//$this->checkInLimit($query, []);
		$data = $this->db->getAll($query . $this->limit);
		//var_dump($query, $data);exit;
		return $data;
	}
	
	private function taxonomyValidation($filters, $catId){
		$taxonomies = '';
		// Формируем условие из фильтров и их значений
		// Узнаем ваилидные фильтры, и валидные значения
		foreach($filters as $filterGroup => $slugs){
			$taxonomies .= "(fg.group_slug = '{$filterGroup}' AND f.slug IN('" . str_replace(',', "','", $slugs) . "')) OR ";
		}
		
		$validTaxonomies = $this->db->getAll('Select DISTINCT fg.group_slug as filter, f.slug as value from categories as c, filters as f, filter_group as fg where c.id = fg.cat_id and f.group_id = fg.group_id and c.id = '.$catId.' and '.substr($taxonomies, 0, -3).' order by fg.group_order DESC, f.filter_order DESC');
		
		return $this->creatingValidFilters($validTaxonomies);
	}
	
	private function creatingValidFilters($validFilters){
		$newValidFilters = [];
		
		foreach($validFilters as $filter){
			$newValidFilters[$filter['filter']][] = $filter['value'];
		}
		
		foreach($newValidFilters as $filters => $values){
			$newValidFilters[$filters] = implode(',', $values);
		}
		
		return $newValidFilters;
	}
	
	
	public function getFiltersHTML($category){
		$filters = $this->db->getAll('SELECT fg.*, f.* from categories as c, filters as f, filter_group as fg where c.id = fg.cat_id and f.group_id = fg.group_id and c.id = '.$category['id'].' order by fg.group_order DESC, f.filter_order DESC');
		$html = '';
		if($filters){
			$html .= '<a href="'. SITE_URL . "{$category['url']}-c{$category['id']}" . '/">Все</a><br>';
			foreach($filters as $filter){
				$filterGroups[$filter['group_name']][] = $filter;
				$filterGroupsBySlug[$filter['group_slug']][] = $filter;
			}
			
			//var_dump($filters, $this->filters, $this->filterAsString, $filterGroupsBySlug);
			foreach($filterGroupsBySlug as $filtersGroupSlug => $filters){
				$html .= '<div class="filters"><div class="title">' . $filters[0]['group_name'] . '</div><div class="content">';
				foreach($filters as $filter){
					$currentFilter = $filter['group_slug'] . '=' . $filter['slug'];
					if($this->filters){
						$newFilters = $this->addFilter($filterGroupsBySlug, $filtersGroupSlug, $filter['slug']);
						$newFiltersString = Filter::stringFromFilters($newFilters);
						$url = str_replace($this->filterAsString . (!$newFiltersString ? '/' : ''), $newFiltersString , FULL_URL);
					}else{
						$url = FULL_URL . $currentFilter . '/';
					}
						
					$html .= "<a href=\"{$url}\">{$filter['name']}</a> ({$filter['count']})<br>";
				} 
				$html .= '</div></div>';
			}
			//echo($html);exit;
		}
		
		return $html;
	}
	
	private function addFilter($allGroups, $currentGroup, $value){//var_dump($currentGroup, $value);
		$filters = $this->filters;
		
		if(isset($filters[$currentGroup])){
			$existsValues = explode(',', $filters[$currentGroup]);
			$newValues = '';
			$filters[$currentGroup] = '';
			foreach($allGroups[$currentGroup] as $filter){
				if(in_array($filter['slug'], $existsValues)){
					if($filter['slug'] != $value)
						$filters[$currentGroup] .= $filter['slug'] . ',';
					else{
						if(count($existsValues) == 1){
							unset($filters[$currentGroup]);
						}else{
							$find = ['~' . $filter['slug'] . ',', ',' . $filter['slug'] . ',', ',' . $filter['slug'] . '~', '~'];
							$filters[$currentGroup] = str_replace($find, '', '~' . $filters[$currentGroup] . '~');
						}
					}
				}elseif($filter['slug'] == $value){
					$filters[$currentGroup] .= $filter['slug'] . ',';
				}
			}
			if(isset($filters[$currentGroup]) && $filters[$currentGroup])
				$filters[$currentGroup] = substr($filters[$currentGroup], 0, -1);
		}else{
			$newFilters = [];
			foreach($filters as $filterGroup => $filtersValue){
				foreach($allGroups as $groupSlug => $group){
					if($filterGroup == $groupSlug){
						$newFilters[$filterGroup] = $filtersValue;
					}elseif($groupSlug == $currentGroup){
						$newFilters[$currentGroup] = $value;
					}
				}
			}
			$filters = $newFilters;
		}
		return $filters;
	}
	
	private function checkExistsFilterInString($string, $filter){
		list($currentFilterGroup, $currentFilterValue) = explode('=', $filter);
		
		if(!isset($this->filters[$currentFilterGroup]))
			return false;
		
		$existingFilterValues = explode(',', $this->filters[$currentFilterGroup]);
		return in_array($currentFilterValue, $existingFilterValues);
		
		
		
		
		var_dump($string, $currentFilterGroup, $this->checkExistsFilterGroup($string, $currentFilterGroup, $currentFilterValue));
		return strpos($string, ';' . $filter) !== false || strpos($string, $filter . ';') !== false || strpos($string . '/', $filter . '/') !== false;
	}
	
	private function checkExistsFilterGroup($string, $group){
		return strpos($string, $group . '=') !== false;
	}
	
	private function checkExistsFilterValue($string, $group, $value){
		return strpos($string, $filterGroup . '=') !== false;
	}
	
	private function filterProcessed($string, $filter){
		list($currentFilterGroup, $currentFilterValue) = explode('=', $filter);
		$groupIsset = isset($this->filters[$currentFilterGroup]);
		
		if(!$groupIsset)
			$this->addFilter($currentFilterGroup, $currentFilterValue, $groupIsset);
		
		
		
		$existingFilterValues = explode(',', $this->filters[$currentFilterGroup]);
		return in_array($currentFilterValue, $existingFilterValues);
	}
	
	private function getFilterGroup($string, $group){
		var_dump($this->filters);
	}
	
	private function getFilterValues($values){
		return explode(',', $values);
	}

	
}
