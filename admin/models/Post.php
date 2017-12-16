<?php

namespace admin\models;

use Jump\Model;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Common;

class Post extends Model{
	
	private $answers = [
		'not_found' => 'Произошла ошибка: Возможно данной записи не существует'
	];
	
	public function postList(){
		return $this->db->getAll('Select * from posts where post_type = ?s', $this->options['type']);
	}
	
	public function termList($type){
		return $type == 'tag' ? $this->getTagList() : $this->getCategoryList();
	}
	
	// ADD
	
	public function addForm(){
		return $this->getFormattedTermList($this->options['category_slug'], $this->options['tag_slug']);
	}
	
	public function addTermForm($type){
		$data['type'] = $type;
		$data['add']  = $type == 'tag' ? 'тег' : 'категорию';
		return $data;
	}
	
	public function addTerm($name, $type, $whisper = false, $slug = '', $description = ''){
		return $this->addTermHelper($name, $type, $whisper, $slug, $description);
	}
	
	public function add(){
		if($this->request->post['title'] == '' || $this->request->post['url'] == '') exit;
		if($this->checkUrlExists($this->request->post['url'])) Msg::json('Введенный адрес уже существует!', 0);
		
				
		$this->db->query('INSERT INTO posts (title, url, content, post_type) VALUES (?s, ?s, ?s, ?s)', $this->request->post['title'], $this->request->post['url'], $this->request->post['content'], $this->options['type']);
		
		$postId = $this->db->insertId();
		
		// Добавляем новые термины
		$this->editTerms(
			$postId, 
			isset($this->request->post['_categories']) ? $this->request->post['_categories'] : [], 
			isset($this->request->post['_tags']) ? $this->request->post['_tags'] : []
		);
		
		Msg::json(array('id' => $postId), 10);
	}
	
	
	public function addTermHelper($name, $type, $whisper = false, $slug = '', $description = ''){
		$termSlug = $type . '_slug';
		// Checking on duplicate term for this taxonomy
		$duplicate = $this->db->getOne('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = \'' . $this->options[$termSlug] .'\'', $name);
		
		if($duplicate){
			if($whisper)
				return false;
			exit;
		}
		
		// Add new term
		$result = $this->db->query('INSERT INTO terms (name, slug) VALUES (?s, ?s)', $name, $slug ?: $name);
		
		if($result){
			$result = $this->db->query('INSERT INTO term_taxonomy (term_id, taxonomy, description) VALUES (?s, ?s, ?s)', $this->db->insertId(), $this->options[$termSlug], $description);
		}
		
		if($whisper)
			return $result;
			
		if($result)
			echo 1;
		
		exit;
	}
	
	
	// EDIT
	
	public function editForm($id){
		if(!$data = $this->db->getRow('Select * from posts where id = ?s', $id)) return 0;
		$data['selfTerms'] = $this->getTermsIdByPostId($id);
		return array_merge($data, $this->getFormattedTermList());
	}
	
	public function editTermForm($id){
		return $this->db->getRow('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.id = ?i', $id);
	}
	
	public function edit(){//var_dump($_POST);exit;
		if($this->request->post['title'] == '' || $this->request->post['url'] == '') return;
		if($this->checkUrlExists($this->request->post['url'], $this->request->post['id'])) Msg::json('Введенный адрес уже существует!', 0);
		
		// Обновлям запись
		$this->db->query('UPDATE posts SET title = ?s, url = ?s, content = ?s, modified = ?s where id = ?i', $this->request->post['title'], $this->request->post['url'], $this->request->post['content'], MyDate::getDateTime(), $this->request->post['id']);
		
		
		// Добавляем новые термины или удаляем ненужные
		$this->editTerms(
			$this->request->post['id'], 
			isset($this->request->post['_categories']) ? $this->request->post['_categories'] : [], 
			isset($this->request->post['_tags']) ? $this->request->post['_tags'] : []
		);
		
		
		Msg::json(array('link' => ROOT_URI . ($this->options['slug'] != 'pages' ? $this->options['slug'] . '/' : '') . $this->request->post['url'] . '/'));
	}
	
	// Добавляем новые термины или удаляем ненужные
	private function editTerms($postId, $comeCategories, $comeTags){
		// -Безопасность- Проверим пришедшие термины на валидность. Невалидные отбросим.
		// Сформируем пришедшие
		$comeTerms = array_merge($comeCategories, $comeTags);
		$comeTerms = $this->checkTermExists($comeTerms);
		
		
		// Проверяем пришедшие категории и теги и пишем или удаляем
		// Берем старые термины
		$oldTerms = $this->db->getAll('Select term_taxonomy_id from term_relationships where object_id = ?i', $postId);
		$del = [];
		
		if($oldTerms){
			foreach($oldTerms as $old){
				$find = false;
				foreach($comeTerms as $key => $come){
					// Если в пришедших есть старый, значит с ним ничего делать не надо, исключаем
					if($old['term_taxonomy_id'] == $come){
						unset($comeTerms[$key]);
						$find = true;
					}
				}
				
				// Если не нашли старый в пришедших, значит этот термин надо удалить
				if(!$find){
					$del[] = $old['term_taxonomy_id'];
				}
			}
		}
			
		// Все пришедшие за исключением старых - новые
		$new = $comeTerms;
		if(!empty($new)){
			$this->db->query('INSERT INTO term_relationships (object_id, term_taxonomy_id) VALUES (' . $postId . ',' . implode('),(' . $postId . ',', $new) . ')');
			$this->changeTermTaxonomyCount($new, true);
		}
			
		if(!empty($del)){
			$this->db->query('Delete from term_relationships where object_id = ' . $postId . ' and term_taxonomy_id IN(' . implode(',', $del) . ')');
			$this->changeTermTaxonomyCount($del, false);
		}
	}
	
	public function editTerm($id, $name, $slug, $description){
		$this->db->query("Update terms SET name = '{$name}', slug = '{$slug}' where id = ?i", $id);
		$this->db->query("Update term_taxonomy SET description = '{$description}' where term_id = ?i", $id);
		$this->request->location(SITE_URL . URI . '?msg=успешно');
	}
	
	// Проверяет пришедшие термины на валидность
	// Принимает пришедшие термины
	// Возвращает идентификаторы валидных терминов
	public function checkTermExists($comeTerms){
		$termsId = [];
		if(!empty($comeTerms)){
			// Возьмем все возможные термины(категории и теги) для данного типа поста
			$termsExists = $this->getTermList($this->options['category_slug'], $this->options['tag_slug']);
			
			
			// Проходим по валидным категориям и смотрим есть ли они в пришедших
			// Оставляем лишь валидные
			
			foreach($termsExists as $term){
				if(in_array($term['id'], $comeTerms)){
					$termsId[] = $term['id'];
				}
			}
		}
		return $termsId;
	}
	
	
	private function changeTermTaxonomyCount($ids, $mark = true){
		$mark = $mark ? '+' : '-';
		$this->db->query("Update term_taxonomy SET count = count {$mark} 1 where term_taxonomy_id IN(" . implode(',', $ids) . ")");
	}
	
	
	// DELETE
	
	public function del($id, $type){
		if($type == 'post')
			$this->delPost($id);
		else
			$this->delTerm($id);
		
	}
	
	private function delPost($id){
		$id = $this->db->getOne('Select id from posts where id = ?i', $id);
		
		if(!$id)
			exit($this->answers['not_found']);
		
		
		
		$this->db->query('Delete from posts where id = ' . $id);
		$this->db->query('Delete from postmeta where post_id = ' . $id);
		$this->db->query('Update term_taxonomy SET count = count - 1 where term_taxonomy_id IN(Select term_taxonomy_id from term_relationships where object_id = ?i)', $id);
		$this->db->query('Delete from term_relationships where object_id = ' . $id);
		Msg::success();
		
	}
	
	private function delTerm($id){
		$term_taxonomy_id = $this->db->getOne('Select term_taxonomy_id from term_taxonomy as tt, terms as t where tt.term_id = t.id and t.id = ?i',  $id);
		
		if(!$term_taxonomy_id)
			exit($this->answers['not_found']);
		
		$this->db->query('Delete from terms where id = ?i', $id);
		$this->db->query('Delete from term_taxonomy where term_taxonomy.term_id = ?i',  $id);
		$this->db->query('Delete from term_relationships where term_taxonomy_id = ?i', $term_taxonomy_id);
		Msg::success();
	}
	
	
	
	public function getTermById($id){
		return $this->db->getAll('Select t.id, t.slug, t.name, tt.taxonomy, tt.term_taxonomy_id, tt.count from terms t, term_taxonomy tt where t.id = tt.term_id and t.id = ?i', $id);
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
		$terms = $this->db->getAll('Select tt.term_id from term_taxonomy as tt, term_relationships as tr where tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ?i order by tt.term_id', $postId);
		
		$data = [];
		if($terms)
			foreach($terms as $term){
				$data[] = $term['term_id'];
			}
		
		return $data;
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
	
	
	private function checkUrlExists($url, $id = ''){
		if(is_numeric($id)) $id = ' and id != ' . $id;
		return $this->db->getOne('Select id from posts where url = ?s and post_type = ?s' . $id, $url, $this->options['type']) ? true : false;
	}
	
	
}