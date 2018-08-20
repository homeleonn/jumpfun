<?php

namespace frontend\models\Post;

class Taxonomy{
	private $db;
	private $select = 'Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and ';
	private $postTypeTaxonomies;
	private static $cache;
	
	public function __construct($db){
		$this->db = $db;
	}
	
	public function getAll($where, $args){
		static $cache;
		if(!isset($cache[$where])) 
			$cache[$where] = call_user_func_array([$this->db, 'getAll'], array_merge([$this->select . $where], [$args]));
		return $cache[$where];
	}
	
	public function getAllByPostTypes($postType){
		if(!$this->postTypeTaxonomies)
			$this->postTypeTaxonomies = $this->db->getAll('Select DISTINCT t.*, tt.* from posts as p, terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id and p.post_type = \''.$postType.'\'');
		return $this->postTypeTaxonomies;
	}
	
	public function filter($terms, $by, $value, $onlyOne = false){
		$result = false;
		foreach($terms as $term){
			if(!isset($term[$by])) return $result;
			if($term[$by] == $value){
				if($onlyOne)
					return $term;
				$result[] = $term;
			}
		}
		return $result;
	}
	
	public function whatIs($terms, $name){
		foreach($terms as $term){
			if($term['name'] == $name)
				return $term['taxonomy'];
		}
		return false;
	}
	
	public function getAllByObjectsIds($objectsIds){
		return $this->db->getAll('Select t.*, tt.*, tr.object_id from terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id IN(?a)', [$objectsIds]);
	}
	
	public function getByTaxonomies($taxonomies = NULL){
		$taxonomies = $taxonomies ?: array_keys(Options::get('taxonomy'));
		if(empty($taxonomies)) return [];
		if(($cache = self::cache($taxonomies)) === NULL)
			self::cache($taxonomies, $this->db->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and tt.taxonomy IN(?a)', [$taxonomies]));
		return self::cache($taxonomies);
	}
	
	public static function cache($key, $value = NULL){
		if(is_array($key)) $key = implode(',', $key);
		if($value === NULL){
			return isset(self::$cache[$key]) ? self::$cache[$key] : NULL;
		}else{
			self::$cache[$key] = $value;
		}
	}
}