<?php

namespace frontend\models\Post;

use Jump\Model;
use Jump\helpers\Filter;
use Jump\helpers\Common;
use Jump\DI\DI;
use frontend\models\Post\Taxonomy;

class Post extends Model{
	use \Jump\helpers\CurrentWork;
	use \Jump\traits\PostTrait;
	
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
	
	private $options;
	private $filters;
	private $limit;
	private $page;
	private $start;
	private $allItemsCount;
	private $select = 'Select * from posts where ';
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	
	public function __construct(DI $di, Taxonomy $taxonomy){
		parent::__construct($di);
		$this->taxonomy = $taxonomy;
	}
	
	public function setOptions($options){
		$this->options = $options;
	}
	
	public function single($url, $id = NULL){
		return $id ? $this->getPostById($id) : $this->getPostByUrl($url);
	}
	
	public function getPostById($id){
		return $this->db->getRow($this->select . 'id = ?i', $id);
	}
	
	public function getPostByUrl($url){
		return $this->db->getRow($this->select . 'url = ?s and post_type = ?s', $url, $this->options['type']);
	}
	
	public function getChildrens($parentId){
		return $this->db->getAll($this->select . 'parent = ?i', (int)$parentId);
	}
	
	public function getPostsByPostType($type){
		$query = $this->select . 'post_type = ?s order by id DESC';
		$this->checkInLimit($query, [$type]);
		return $this->db->getAll($query . $this->limit, $type);
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
		
		if(strcmp($filtersAsString, $validFiltersAsString) !== 0){var_dump(1);exit;
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
		var_dump($postType);
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
		if(!$termName = $this->checkTermExists($taxonomy, $value)) 
			return 0;
		$query = $this->select . 'id IN(Select DISTINCT p.id from ' . $this->relationship . ' and t.slug = ?s and tt.taxonomy = ?s) order by id DESC';
		$post = $this->getAll($query, [$value, $taxonomy]);
		$post['termName'] = $termName;
		return $post;
	}
	
	private function checkTermExists($taxonomy, $value){
		return $this->db->getOne('Select t.name from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = ?s', $value, $taxonomy);
	}
	
	public function getTermNameByTermSlug($slug){
		return $this->db->getOne('Select name from terms where slug = ?s LIMIT 1', $slug);
	}
	
	
	public function getMeta($postId){
		return $this->metaProcessing($this->db->getAll('Select meta_key, meta_value from postmeta where post_id = ?i', $postId));
	}
	
	private function metaProcessing($meta){
		if(!$meta) return false;
		$metaNew = [];
		foreach($meta as $m)
			$metaNew[$m['meta_key']] = $m['meta_value'];
		return $metaNew;
	}
	
	private function checkInLimit($query, $params){
		if(!$this->page || $this->page == 1) return;
		$this->allItemsCount = (int)call_user_func_array([$this->db, 'getOne'], array_merge([str_replace('Select *', 'Select COUNT(*) as count', $query)], $params));
		
		if($this->allItemsCount && $this->allItemsCount <= $this->start){
			$newUrl = Filter::clearFilter(FULL_URL, 'page=' . $this->page, ';');
			if(!$this->filters)
				$newUrl = substr($newUrl, 0, -1);
			
			$this->request->location($newUrl);
		}
	}
	
	private function getAll($query, $params){
		$this->checkInLimit($query, $params);
		return call_user_func_array([$this->db, 'getAll'], array_merge([$query . $this->limit], $params));
	}
	
	public function getAllItemsCount(){
		return $this->allItemsCount;
	}
	
	public function getFiltersHTML($taxonomies, $postType, $postSlug){
		if(!isset($taxonomies)) return false;
		if(!$terms = $this->db->getAll('Select DISTINCT t.*, tt.* from ' . $this->relationship . ' and p.post_type = \''.$postType.'\' and tt.taxonomy IN(\'' . implode("','", $taxonomies) . '\')')) return false;
		
		return $this->getFiltersHTMLForList($terms, $postSlug);
	}
	
	private function getFiltersHTMLForList($terms, $postSlug){
		$html['all']= '<a href="'. SITE_URL . $postSlug . '/">Все</a><br>';
		$tmpTaxonomy = $terms[0]['taxonomy'];
		foreach($terms as $key => $term){
			if(!isset($html[$term['taxonomy']])){
				$html[$term['taxonomy']] = '<div class="filters"><div class="title">' . $term['taxonomy'] . '</div><div class="content">';
				if($term['taxonomy'] != $tmpTaxonomy) $html[$tmpTaxonomy] .= '</div></div>';
			}
			$html[$term['taxonomy']] .= "<a href=\"" . SITE_URL . "{$postSlug}/{$term['taxonomy']}/{$term['slug']}/\">{$term['name']}</a> ({$term['count']})";
			$tmpTaxonomy = $term['taxonomy'];
		}
		$html[$tmpTaxonomy] .= '</div></div>';
		
		return implode('', $html);
	}
	
	public function getTermsByPostId($postId, $taxonomies){
		$terms = $this->db->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ' . $postId . ' and tt.taxonomy IN(\'' . implode("','", $this->options['taxonomy']) . '\')');
		if(!$terms) return false;
		$html = [];
		if($terms){
			foreach($terms as $key => $term){
				if(!isset($html[$term['taxonomy']])) $html[$term['taxonomy']] = ($key ? '<br>' : '') . $term['taxonomy'] . ': ';
				$html[$term['taxonomy']] .= "<a href='" . SITE_URL . "{$this->options['slug']}/{$term['taxonomy']}/{$term['slug']}/'>{$term['name']}</a><br>";
			}
		}
		return implode('', $html);
	}
	
	
	public function getTermsListByTaxonomy($taxonomy, $postType){
		if(!$terms = $this->db->getAll('Select DISTINCT t.*, tt.* from ' . $this->relationship . ' and p.post_type = \''.$postType.'\' and tt.taxonomy = \''.$taxonomy.'\'')) return false;
		
		foreach($terms as $t){
			$html[] = "<a href='" . SITE_URL . "{$this->options['slug']}/{$t['taxonomy']}/{$t['slug']}/'>{$t['name']}</a>";
		}
		
		return $html;
	}
}