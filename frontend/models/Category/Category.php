<?php

namespace frontend\models\category;

use Jump\Model;
use Jump\helpers\Filter;

class Category extends Model{
	use \Jump\traits\PostTrait;
	
	private $selectedFilters = [];
	
	public function getSingleCategory($id, $url){
		return $this->db->getRow('Select * from categories where id = ?i and url = ?s', $id, $url);
	}
	
	public function getCategoryById($id){
		return $this->db->getRow('Select * from categories where id = ?i', $id);
	}
	
	public function getCategoriesByParent($parentId){
		return $this->db->getRow('Select * from categories where parent = ?i', $parentId);
	}
	
	
	public function getProductsByFilters($filters, $catId){
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
		
		$data = $this->getProductsByFilters1($filters);
		return $data;
	}
	
	private function getProductsByFilters1($filters, $counting = false){
		$sqlFilters = '';
		foreach($filters as $filterGroup => $slugs){
			$sqlFilters .= " p.id IN (Select p.id from products as p, filters as f, filter_group as fg, relationships as r where p.cat_id = fg.cat_id and f.group_id = fg.group_id and f.id = r.filter_id and r.product_id = p.id and (fg.group_slug = '{$filterGroup}' AND (f.slug = '" . str_replace(',', "' OR f.slug = '", $slugs) . "'))) AND";
		}
		$col = !$counting ? 'p.*' : 'COUNT(*) as count';
		$query = 'SELECT '.$col.' from products as p where '.substr($sqlFilters, 0, -4).' order by id DESC';
		//$this->checkInLimit($query, []);
		$products = $this->db->getAll($query . $this->limit);
		return $products;
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
	
	
	public function getFiltersHTML(&$category, $products){
		$filters = $this->db->getAll('SELECT fg.*, f.* from categories as c, filters as f, filter_group as fg where c.id = fg.cat_id and f.group_id = fg.group_id and c.id = '.$category['id'].' order by fg.group_order DESC, f.filter_order DESC');
		$html = '';
		if($filters){
			$title = [];
			foreach($filters as $filter){
				$filterGroupsBySlug[$filter['group_slug']][] = $filter;
			}
			
			//var_dump($filterGroupsBySlug, $products, $possibleRelations);
			
			foreach($filterGroupsBySlug as $filtersGroupSlug => $filters){
				$groupSelected = $this->checkGroupOnSelected($filtersGroupSlug);
				$html .= '<div class="filters"><div class="title">' . $filters[0]['group_name'] . '</div><div class="content">';
				foreach($filters as $filter){
					$currentFilter = $filter['group_slug'] . '=' . $filter['slug'];
					if($this->filters){
						list($url, $filters2) = $this->urlFromFilter($filterGroupsBySlug, $filtersGroupSlug, $filter['slug']);
					}else{
						$filters2 = $currentFilter;
						$url = FULL_URL . $currentFilter . '/';
					}
					if(mb_strlen(FULL_URL) > mb_strlen($url)){
						$this->selectedFilters[] = "<a style='border: 1px lightblue solid; padding: 3px 6px; margin: 3px; border-radius: 20px;' href=\"{$url}\">{$filter['name']} <span style='font-weight: bold; color:red;'>X</span></a>";
						$title[$filters[0]['group_name']][] = $filter['name'];
						$html .= "<a href=\"{$url}\"><input type='checkbox' checked> {$filter['name']}</a><br>";
						$groupSelected = true;
					}else{
						$count = $filter['count'];
						if($filters2){
							$filters1 = [];
							foreach(explode(';', $filters2) as $filter3){
								list($group, $values) = explode('=', $filter3);
								$filters1[$group] = $filtersGroupSlug != $group ? $values : $filter['slug'];
							}
						}
						
						if(($count = $this->getCache($cacheFileName = $this->setCacheFileName($category['id'], $filters1), false)) === false){
							$count = $this->getProductsByFilters1($filters1, true)[0]['count'];
							$this->setCache($cacheFileName, $count?:'0');
						}
						
						$c = $count;
						if($groupSelected && $count)
							$count = '+' . $count;
						
						if(!$c)
							$html .= "<input type='checkbox' disabled> <span style='cursor: default;color: #ccc;'>{$filter['name']} ({$count})</span><br>";
						else
							$html .= "<a href=\"{$url}\"><input type='checkbox'> {$filter['name']}</a> ({$count})<br>";
					}
				}
				$html .= '</div></div>';
			}//exit;
			$this->selectedFilters[] = '<a style="border: 1px red solid; padding: 3px 6px; margin: 3px; border-radius: 20px;" href="'. SITE_URL . "{$category['url']}-c{$category['id']}" . '/">Сбросить</a>';
			$category['title'] = $this->setCategoryTitleFromSelectedFilters($title) . $category['title'];
			
		}
		return $html;
	}
	
	private function setCacheFileName($catId, $filters){
		$cacheFilename = ROOT . 'content/uploads/cache/shop/counting-c' . $catId . '-z-';
		$groups = '';
		foreach($filters as $filterGroup => $values){
			$groups .= $filterGroup . '-';
			$cacheFilename .= $filterGroup . $values;
		}
		$cacheFilename .= '--' . $groups;
		return $cacheFilename . '.php';
	}
	
	private function urlFromFilter($allGroups, $currentGroup, $value){//var_dump($currentGroup, $value);
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
		$newFiltersString = Filter::stringFromFilters($filters);
		$url = str_replace($this->filterAsString . (!$newFiltersString ? '/' : ''), $newFiltersString , FULL_URL);
		
		return [$url, $newFiltersString];
	}
	
	
	
	private function filterProcessed($string, $filter){
		list($currentFilterGroup, $currentFilterValue) = explode('=', $filter);
		$groupIsset = isset($this->filters[$currentFilterGroup]);
		
		if(!$groupIsset)
			$this->addFilter($currentFilterGroup, $currentFilterValue, $groupIsset);
		$existingFilterValues = explode(',', $this->filters[$currentFilterGroup]);
		return in_array($currentFilterValue, $existingFilterValues);
	}
	
	public function getSelectedFiltersHTML(){
		return count($this->selectedFilters) > 1 ? ('<div style="margin: 3px 3px 15px;">' . implode("\n", $this->selectedFilters) . '</div>') . '' : '';
	}
	
	public function setCategoryTitleFromSelectedFilters($title){
		$titleHTML = '';
		if(!empty($title)){
			foreach($title as $filterName => $filterValues){
				$titleHTML .= $filterName . ': ' . implode(', ', $filterValues) . '; ';
			}
		}
		return $titleHTML;
	}
	
	private function checkGroupOnSelected($filterGroup){
		return $this->filters  ? strpos(Filter::stringFromFilters($this->filters), $filterGroup . '=') !== false : false;
	}
	
	private function getCache($cacheFileName, $outNow = true, $delayHours = 24){
		if(file_exists($cacheFileName) && filemtime($cacheFileName) > time() - $delayHours * 3600){
			$data = file_get_contents($cacheFileName);
			if($data === false) return false;
			if($outNow){
				echo $data;
			}
			else return $data;
			return true;
		}
		ob_start();
		return false;
	}

	private function setCache($cacheFileName, $data = false){
		if($data === false) $data = ob_get_clean();
		file_put_contents($cacheFileName, (string)$data, LOCK_EX);
		return $data;
	}
	
}
