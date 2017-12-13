<?php

namespace admin\models;

use Jump\Model;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Common;

class Post extends Model{
	public function postList(){
		return $this->db->getAll('Select * from posts where post_type = ?s', $this->postType);
	}
	
	public function categoryList(){
		return $this->getCategoryList();
	}
	
	// ADD
	
	public function addForm(){
		return $this->getFormattedTermList($this->options['category_slug'], $this->options['tag_slug']);
	}
	
	public function add(){
		$params = $this->di->get('request')->post;
		if($params['title'] == '' || $params['url'] == '') exit;
		if($this->checkUrlExists($params['url'])) Msg::json('Введенный адрес уже существует!', 0);
		
		// Проверим валидность пришедших категорий, в которые пользователь хочет занести пост
		$categoryValid = false;
		if(isset($params['_categories'])){
			
			// Возьмем все возможные категории для данного типа поста
			$terms = $this->getTermList($this->options['category_slug']);
			
			// Кол-во пришедших категорий
			$countParams = count($params['_categories']);
			
			// Проходим по валидным категориям и смотрим есть ли они в пришедших
			// Если есть, декрементируем количество, если останется 0 значит порядок
			$categoriesId = [];
			foreach($terms as $term){
				if(in_array($term['slug'], $params['_categories'])){
					$countParams--;
					$categoriesId[$term['slug']] = $term['term_taxonomy_id'];
				}
			}
			
			if(0 == $countParams) $categoryValid = true;
		}
		
		
		
		$query = "INSERT INTO posts (title, url, content, post_type) VALUES (?s, ?s, ?s, ?s)";
				
		$this->db->query($query, $params['title'], $params['url'], $params['content'], $this->postType);
		
		$postId = $this->db->insertId();
		
		
		if($categoryValid){
			$values = $ids = '';
			foreach($params['_categories'] as $category){
				$values .= "({$postId}, {$categoriesId[$category]}),";
				$ids .= $categoriesId[$category] . ',';
			}
			$this->db->query('INSERT INTO term_relationships (object_id, term_taxonomy_id) VALUES ' . substr($values, 0, -1));
			$this->db->query('Update term_taxonomy SET count = count + 1 where term_taxonomy_id IN('.substr($ids, 0, -1).')');
		}
		
		Msg::json(array('id' => $postId), 10);
	}
	
	
	public function addTerm($termName, $type){
		// Checking on duplicate term for this taxonomy
		$duplicate = $this->db->getOne('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = \'' . $this->options[($type . '_slug')] .'\'', $termName);
		
		if($duplicate) exit;
		
		// Add new term
		$result = $this->db->query('INSERT INTO terms (name, slug) VALUES (?s, ?s)', $termName, $termName);
		
		if($result){
			$result = $this->db->query('INSERT INTO term_taxonomy (term_id, taxonomy) VALUES (?s, ?s)', $this->db->insertId(), $this->options[($type . '_slug')]);
			if($result) echo 1;
		}
		
		exit;
	}
	
	
	// EDIT
	
	public function editForm($id){
		$data = $this->db->getRow('Select * from posts where id = ?s', $id);
		$data['selfCateg'] = $this->db->getRow('Select * from posts where id = ?s', $id);
		return array_merge($data, $this->getFormattedTermList());
	}
	
	public function edit(){var_dump($_POST);exit;
		$params = $this->di->get('request')->post;
		if($params['title'] == '' || $params['url'] == '') return;
		if($this->checkUrlExists($params['url'], $params['id'])) Msg::json('Введенный адрес уже существует!', 0);
		
		$this->db->query('UPDATE posts SET title = ?s, url = ?s, content = ?s, modified = ?s where id = ?i', $params['title'], $params['url'], $params['content'], MyDate::getDateTime(), $params['id']);
		
		Msg::json(array('link' => ROOT_URI . $this->options['slug'] . $params['url'] . '/'));
	}
	
	
	// DELETE
	
	public function del($id){
		$id = $this->db->getOne('Select id from posts where id = ?i', $id);
		
		if($id){
			$result = $this->db->query('Delete from posts where id = ' . $id);
			$this->db->query('Delete from postmeta where post_id = ' . $id);
			$this->db->query('Delete from term_relationships where object_id = ' . $id);
		}
			
		if($result) Msg::success();
		
		exit('Произошла ошибка: Возможно данной записи не существует');
	}
	
	
	
	public function getCategoryList(){
		return $this->getTermList($this->options['category_slug']);
	}
	
	public function getTagList(){
		return $this->getTermList($this->options['tag_slug']);
	}
	
	public function getTermList($taxonomy, $alternateTax = null){
		$alternateTax = $alternateTax ? ' OR tt.taxonomy = \''.$alternateTax.'\'' : '';
		return $this->db->getAll('Select t.id, t.slug, t.name, tt.taxonomy, tt.term_taxonomy_id, tt.count from terms t, term_taxonomy tt where t.id = tt.term_id and (tt.taxonomy = \''.$taxonomy.'\''.$alternateTax.') order by t.id ASC');
	}
	
	public function getTermsIdByPostId($postId){
		return $this->db->getAll('Select tt.term_id from term_taxonomy as tt, term_relationships as tr where tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ?i order by tt.term_id', $postId);
	}
	
	public function getFormattedTermList(){
		$data['terms'] = $this->getTermList($this->options['category_slug'], $this->options['tag_slug']);
		return $data;
		
		$terms = $this->getTermList($this->options['category_slug'], $this->options['tag_slug']);

		$data = NULL;
		foreach($terms as $term){
			//$data[(($term['taxonomy'] == $this->options['category_slug']) ? '_category' : '_tag')][$term['slug']] = $term['name'];
			$data[(($term['taxonomy'] == $this->options['category_slug']) ? '_category' : '_tag')][$term['id']] = $term;
		}//var_dump($data);exit;
		
		return $data;
	}
	
	public function checkTermExists($taxonomy, $terms){
		return $this->db->getAll('Select count(*) from terms t, term_taxonomy tt where t.id = tt.term_id and (tt.taxonomy = \''.$taxonomy.'\') and t.slug IN('.$terms.') order by t.id ASC');
	}
	
	
	private function checkUrlExists($url, $id = ''){
		if(is_numeric($id)) $id = ' and id != ' . $id;
		return $this->db->getOne('Select id from posts where url = ?s and post_type = ?s' . $id, $url, $this->postType) ? true : false;
	}
}