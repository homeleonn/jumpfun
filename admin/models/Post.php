<?php

namespace admin\models;

use Jump\Model;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Common;
use Jump\helpers\Transliteration;

class Post extends Model{
	
	private $answers = [
		'not_found' => 'Произошла ошибка: Возможно данной записи не существует'
	];
	
	public function postList(){
		return $this->hierarchy($this->getPossibleParents(NULL, NULL, true)[0], 0, 0, 'table');
		
		//return $this->db->getAll('Select * from posts where post_type = ?s', $this->options['type']);
		//var_dump($this->getPossibleParents(NULL, NULL, true));exit;
	}
	
	public function termList($type){
		return $type == 'tag' ? $this->getTagList() : $this->getCategoryList();
	}
	
	// ADD
	
	public function addForm(){
		if(!Common::isPage()) 
			$data = $this->getFormattedTermList($this->options['category_slug'], $this->options['tag_slug']);
		
		$data['listForParents'] = $this->getPossibleParents();
		return $data;
	}
	
	private function getPossibleParents($selfId = NULL, $parent = NULL, $getPosts = false){
		$addSql = $selfId ? ' AND id != ' . $selfId : '';
		$posts = $this->db->getAll('Select * from posts where post_type IN(\'page\')'.$addSql.' order by parent');
		$postsToParents = [];
		foreach($posts as $post){
			$postsToParents[$post['parent']][] = $post;
		}
		
		$postsToParents = array_reverse($postsToParents, true);
		foreach($postsToParents as &$posts){
			foreach($posts as &$post){
				if(isset($postsToParents[$post['id']])){
					$post['children'] = $postsToParents[$post['id']];
					unset($postsToParents[$post['id']]);
				}
			}
		}
		
		if($getPosts)
			return $postsToParents;
		return '<select name="parent"><option value="0">(нет родительской)</option>' . $this->hierarchy($postsToParents[0], 0, $parent) . '</select>';
	}
	
	private function hierarchy($posts, $level, $parent, $type = 'select', $urlHierarchy = ''){
		$html = '';
		foreach($posts as $post){
			if($type == 'select'){
				$html .= '<option '.($post['id'] == $parent ? 'selected' : '').' value="' . $post['id'] . '">' . str_repeat('&nbsp;', $level * 3) . ($level ? '&#8735;'  : '') . (mb_strlen($post['title']) > 46 ? mb_substr($post['title'], 0, 45) . '...' : $post['title']) . '</option>';
			}elseif($type = 'table'){
				$html .= $this->hierarchyListHtml($post, $level, $urlHierarchy);
			}
			
			if(isset($post['children'])){
				$urlHierarchy .= $post['url'] . '/';
				$html .= $this->hierarchy($post['children'], $level + 1, $parent, $type, $urlHierarchy);
			}
			$urlHierarchy = '';
		}
		return $html;
	}
	
	private function hierarchyListHtml($page, $level, $urlHierarchy){//var_dump($page);
		$link = '<a target="_blank" href="' . ROOT_URI . $urlHierarchy .(Common::isPage() ? '' : $this->options['slug'] . '/') . $page['url'] . '/">' . $page['title'] . '</a>';
		ob_start();
		?>
		<tr>
			<td><?=str_repeat('&mdash;', $level)?> <?=$link;?></td>
			<td><?=$page['created'];?></td>
			<td>
				<a title="<?=$this->options['edit']?>"  target="blank" href="<?=SITE_URL?>admin/<?=$this->options['slug']?>/edit/<?=$page['id'];?>/">
					<span class="icon-pencil block"></span>
				</a>
			</td>
			<td>
				<a href="javascript:void(0);" title="<?=$this->options['delete']?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$this->options['slug']?>',<?=$page['id'];?> );">
					<span class="icon-cancel red block"></span>
				</a>
			</td>
		</tr>
		<?php
		return ob_get_clean();
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
		if($this->request->post['title'] == '') exit;
		
		$url = Transliteration::run($this->request->post['title']);
		$url = $this->checkUrl($url);
				
		$this->db->query('INSERT INTO posts (title, url, content, parent, post_type) VALUES (?s, ?s, ?s, ?i, ?s)', $this->request->post['title'], $url, $this->request->post['content'], $this->request->post['parent'], $this->options['type']);
		
		$postId = $this->db->insertId();
		
		// Добавляем новые термины
		$this->editTerms(
			$postId, 
			isset($this->request->post['_categories']) ? $this->request->post['_categories'] : [], 
			isset($this->request->post['_tags']) ? $this->request->post['_tags'] : []
		);
		
		Msg::json(array('id' => $postId), 10);
	}
	
	private function checkUrl($url, $adder = 0){
		$newUrl = $url . ($adder ? '-' . $adder : '');
		if($this->checkUrlExists($newUrl)){
			$newUrl = $this->checkUrl($url, $adder + 1);
		}
		return $newUrl;
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
		if(!$post = $this->db->getRow('Select * from posts where id = ?s', $id)) return 0;
		$post['listForParents'] = $this->getPossibleParents($post['id'], $post['parent']);
		if(Common::isPage()){
			return $post;
		}
		$post['selfTerms'] = $this->getTermsIdByPostId($id);
		return array_merge($post, $this->getFormattedTermList());
	}
	
	public function editTermForm($id){
		return $this->db->getRow('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.id = ?i', $id);
	}
	
	public function edit(){//var_dump($_POST);exit;
		if($this->request->post['title'] == '' || $this->request->post['url'] == '') return;
		if($this->checkUrlExists($this->request->post['url'], $this->request->post['id'])) Msg::json('Введенный адрес уже существует!', 0);
		
		// Обновлям запись
		$this->db->query('UPDATE posts SET title = ?s, url = ?s, content = ?s, parent = ?i, modified = ?s where id = ?i', $this->request->post['title'], $this->request->post['url'], $this->request->post['content'], $this->request->post['parent'], MyDate::getDateTime(), $this->request->post['id']);
		
		
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
		if(!empty($comeTerms)) return;
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
	}
	
	
	private function checkUrlExists($url, $id = ''){
		if(is_numeric($id)) $id = ' and id != ' . $id;
		return $this->db->getOne('Select id from posts where url = ?s and post_type = ?s' . $id, $url, $this->options['type']) ? true : false;
	}
	
	
}