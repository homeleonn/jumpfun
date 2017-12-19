<?php

namespace cms\models;

use Jump\Model;
use Jump\helpers\Filter;

class Post extends Model{
	use \Jump\helpers\CurrentWork;
	
	private $id;
	private $url;
	private $title;
	private $content;
	private $tags;
	private $post_type;
	private $parent;
	private $autor;
	private $status;
	private $comment_status;
	private $comment_count;
	private $visits;
	private $created;
	private $modified;
	
	private $filters;
	private $limit;
	private $page;
	private $start;
	private $select = 'Select * from posts where ';
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	
	public function single($url, $id = NULL){
		return $id ? $this->getPostById($id) : $this->getPostByUrl($url);
	}
	
	public function getPostById($id){
		return $this->db->getRow($this->select . 'id = ?i', $id);
	}
	
	public function getPostByUrl($url){
		return $this->db->getRow($this->select . 'url = ?s and post_type = ?s', $url, $this->options['type']);
	}
	
	public function getPostsByPostType(){
		$query = $this->select . 'post_type = ?s order by id DESC';
		$this->checkInLimit($query, [$this->options['type']]);
		return $this->db->getAll($query . $this->limit, $this->options['type']);
	}
	
	public function getPostsByFilters($filters, $postType){
		$sqlFilters = '';
		// Соберем таксономии и проверим их валидность
		// Если найдутся невалидные, нужно вырезать из запроса данный фильтр и перенаправить
		// Если валидных меньше чем присланных, проходимся по валидным, сверяемся с присланными, сохраняем невалидные присланные, формируем и режем
		$validFilters = $this->taxonomyValidation($filters, $postType);
		
		$filtersAsString = Filter::stringFromFilters($filters);
		$validFiltersAsString = Filter::stringFromFilters($validFilters);
	
		//var_dump($filters, $validFilters, $filtersAsString, $validFiltersAsString);
		
		if(strcmp($filtersAsString, $validFiltersAsString) !== 0){echo 1;exit;
			$this->request->location(str_replace($filtersAsString . (!$validFiltersAsString ? '/' : ''), $validFiltersAsString, FULL_URL), 301);
		}
		
		// Формируем условие из валидных фильтров
		foreach($validFilters as $taxonomy => $slugs){
			$sqlFilters .= " (tt.taxonomy = '{$postType}-{$taxonomy}' and t.slug IN('" . str_replace(',', "','", $slugs) . "')) OR";
		}
		
		$query = $this->select . 'id IN(Select DISTINCT p.id from ' . $this->relationship . ' and ('.substr($sqlFilters, 0, -3).')) order by id DESC';
		$this->checkInLimit($query, []);
		$data = $this->db->getAll($query . $this->limit);
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
	
	public function getPostList($taxonomy, $value){
		// Проверим есть ли вообще такой термин
		if(!$this->checkTermExists($taxonomy, $value)) 
			return 0;
		$query = $this->select . 'id IN(Select DISTINCT p.id from ' . $this->relationship . ' and t.slug = ?s and tt.taxonomy = ?s) order by id DESC';
		$this->checkInLimit($query, [$value, $taxonomy]);
		return $this->db->getAll($query . $this->limit, $value, $taxonomy);
	}
	
	private function checkTermExists($taxonomy, $value){
		return $this->db->getOne('Select t.id from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = ?s', $value, $taxonomy);
	}
	
	public function getMeta($postId){
		return $this->metaProcessing($this->db->getAll('Select meta_key, meta_value from postmeta where post_id = ?i', $postId));
	}
	
	private function metaProcessing($meta){
		if(!$meta) return false;
		
		$metaNew = [];
		
		foreach($meta as $m){
			$metaNew[$m['meta_key']] = $m['meta_value'];
		}
		
		return $metaNew;
	}
	
	private function checkInLimit($query, $params){
		$countItems = (int)call_user_func_array([$this->db, 'getOne'], array_merge([str_replace('Select *', 'Select COUNT(*) as count', $query)], $params));
			//var_dump($countItems, $this->start, Filter::clearInvalidFilter(FULL_URL, 'page=' . $this->page, ';'));exit;
		if($countItems <= $this->start){
			$newUrl = Filter::clearInvalidFilter(FULL_URL, 'page=' . $this->page, ';');
			if(!$this->filters)
				$newUrl = substr($newUrl, 0, -1);
			//var_dump($countItems, $this->start, $newUrl);exit;
			$this->request->location($newUrl);
		}
	}
	
	public function setLimit($page, $perPage){
		$this->page = (int)$page;
		$this->start = ($this->page - 1 ) * 1;
		$this->limit = ' LIMIT ' . $this->start . ',' . $perPage;
	}
	
	public function setFilters($filters){
		$this->filters = $filters;
	}
}