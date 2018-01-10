<?php

namespace admin\models\Post;

use Jump\Model;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Common;

class Post extends Model{
	private $allPosts;
	private $answers = [
		'not_found' => 'Произошла ошибка: Возможно данной записи не существует'
	];
	private $select = 'Select * from posts where ';
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	
	
	public function setOptions($options){
		$this->options = $options;
	}
	
	public function postList(){
		$postsTable = $this->hierarchy($this->getPossibleParents(NULL, NULL, true)[0], 0, 0, 'table');
		return !$postsTable ? '' : '<table class="mytable"><tr align="center"><td>title/url</td>'.($this->options['taxonomy'] ? '<td width="10%">Метки</td>' : '').'<td width="1%">Дата публикации</td></tr>' . $postsTable . '</table>';
		//return $this->db->getAll('Select * from posts where post_type = ?s', $this->options['type']);
		//var_dump($this->getPossibleParents(NULL, NULL, true));exit;
	}
	
	public function termList($term){
		return $this->getTermList($term);
	}
	
	// ADD
	
	public function addForm(){
		$data = [];
		if($this->options['taxonomy']) 		$data['terms'] = $this->getTermList(array_keys($this->options['taxonomy']));
		if($this->options['hierarchical']) 	$data['listForParents'] = $this->getPossibleParents();
		return $data;
	}
	
	/**
	 *  Return of possible parents for a post
	 *  
	 *  @param int $selfId post id for which returns parents
	 *  @param int $parent post parent
	 *  @param bool $getPosts return post without html
	 *  
	 *  @return
	 */
	private function getPossibleParents($selfId = NULL, $parent = NULL, $getPosts = false){
		
		$allPosts = $this->getAllPosts();
		foreach($allPosts as $post)
			$ids[] = $post['id'];
		$postsTerms = $this->getTermsByPostsId($ids);
		foreach($allPosts as $post){
			if($post['id'] == $selfId) continue;
			$post['_terms'] = isset($postsTerms[$post['id']]) ? $postsTerms[$post['id']] : [];
			$postsToParents[$post['parent']][] = $post;
		}
		//var_dump($postsToParents);exit;
		$postsToParents = array_reverse($postsToParents, true);
		if($this->options['hierarchical'])
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
		return '<select style="max-width: 100%;"name="parent"><option value="0">(нет родительской)</option>' . $this->hierarchy($postsToParents[0], 0, $parent) . '</select>';
	}
	
	private function getTermsByPostsId($ids){
		if(!$terms = $this->db->getAll('Select t.*, tt.*, tr.object_id from terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tr.term_taxonomy_id = tt.term_taxonomy_id and object_id IN('.implode(',', $ids).') order by t.id ASC')) return false;
		foreach($terms as $t){
			$termsByObject[$t['object_id']][] = $t;
		}
		return $termsByObject;
	}
	
	/**
	 *  form html post list like hierarchy
	 *  
	 *  @param array $posts 		array of posts
	 *  @param int $level 			hierarchy level
	 *  @param int $parent 			current post parent
	 *  @param string $type 		how output html
	 *  @param string $urlHierarchy built url hierarchy for link at each hierarchy level
	 *  
	 *  @return html code
	 */
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
			
			if(!$post['parent'])
				$urlHierarchy = '';
		}
		return $html;
	}
	
	private function hierarchyListHtml($page, $level, $urlHierarchy){//var_dump($page);exit;
		$link = '<a target="_blank" href="' . ROOT_URI . $urlHierarchy .(Common::isPage() ? '' : $this->options['rewrite']['slug'] . '/') . $page['url'] . '/">перейти</a>';
		$edit = '<a target="blank" href="' . SITE_URL . 'admin/' . $this->options['type'] . '/edit/' . $page['id'] . '/">%s</a>';
		ob_start();
		?>
			<tr>
				<td class="admin-page-list">
					<?=str_repeat('&mdash;', $level) . ' ' . sprintf($edit, $page['title']);?>
					<div style="position: absolute;">
						[<?=$link;?>]
						[<a href="#">свойства</a>]
						[<?=sprintf($edit, 'изменить');?>]
						[<a style="color: red;" href="javascript:void(0);" title="<?=$this->options['delete']?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$this->options['type']?>',<?=$page['id'];?> );">удалить</span></a>]
					</div>
				</td>
				<?php 
					if($this->options['taxonomy'] && isset($page['_terms'])){
						echo '<td>';
						ob_start();
						foreach($page['_terms'] as $term){
							echo '<a href="'. SITE_URL . $this->options['rewrite']['slug'] . '/' . $term['taxonomy'] . '/' . $term['slug'] . '/">'.$term['name'].'</a>, ';
						}
						echo substr(ob_get_clean(), 0, -2);
						echo '</td>';
						
					}
				?>
				<td><?=$page['created'];?></td>
			</tr>
		<?php
		return ob_get_clean();
	}
	
	
	private function postKeysToParents($posts, $excludePostId = NULL){
		foreach($posts as $post){
			if($post['id'] == $excludePostId) continue;
			$postsToParents[$post['parent']][] = $post;
		}
		return $postsToParents;
	}
	
	public function addTermForm($term){
		$data['term'] = $data['add'] = $term;
		return $data;
	}
	
	public function addTerm($name, $term, $whisper = false, $slug = '', $description = ''){
		return $this->addTermHelper($name, $term, $whisper, $slug, $description);
	}
	
	public function add($title, $url, $content, $parent, $posType, $terms){
		$this->db->query('INSERT INTO posts (title, url, content, parent, post_type) VALUES (?s, ?s, ?s, ?i, ?s)', $title, $url, $content, $parent, $posType);
		
		$postId = $this->db->insertId();
		
		// Добавляем новые термины
		$this->editTerms($postId, $terms);
		
		Msg::json(array('id' => $postId), 10);
	}
	
	
	
	public function checkUrl($url, $parent, $adder = 0){
		$newUrl = $url . ($adder ? '-' . $adder : '');
		if($this->checkUrlExists($newUrl, $parent)){
			$newUrl = $this->checkUrl($url, $parent, $adder + 1);
		}
		return $newUrl;
	}
	
	
	
	public function addTermHelper($name, $term, $whisper = false, $slug = '', $description = ''){
		// Checking on duplicate term for this taxonomy
		$duplicate = $this->db->getOne('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = \'' . $term .'\'', $name);
		
		if($duplicate){
			if($whisper) return false;
			exit;
		}
		
		// Add new term and taxonomy
		if($result = $this->db->query('INSERT INTO terms (name, slug) VALUES (?s, ?s)', $name, $slug ?: $name)){
			$result = $this->db->query('INSERT INTO term_taxonomy (term_id, taxonomy, description) VALUES (?s, ?s, ?s)', $this->db->insertId(), $term, $description);
		}
		
		if($whisper) return $result;
		if($result)  echo 1;
		exit;
	}
	
	
	// EDIT
	
	public function editForm($id){
		if(!$post = $this->db->getRow('Select * from posts where id = ?s', $id)) return 0;
		if($this->options['hierarchical']){
			$post['urlHierarchy'] = $this->getUrlHierarchy($post['id']);
			$post['listForParents'] = $this->getPossibleParents($post['id'], $post['parent']);
		}else{
			$post['urlHierarchy'] = $this->options['rewrite']['slug'] . '/';
		}
		if(Common::isPage()){
			return $post;
		}
		$post['selfTerms'] = $this->getTermsIdByPostId($id);
		return array_merge($post, $this->getFormattedTermList());
	}
	
	public function getUrlHierarchy($childId){
		$posts = $this->getAllPosts();
		foreach($posts as $post){
			$postsKeysId[$post['id']] = $post;
		}
		$hierarchyUrl = '';
		if(isset($postsKeysId[$postsKeysId[$childId]['parent']]))
			$parent[] = $postsKeysId[$postsKeysId[$childId]['parent']];
		$i = 0;
		while(isset($parent[$i])){
			$hierarchyUrl .= '/' . $parent[$i]['url'];
			if($parent[$i]['parent']){
				$parent[] = $postsKeysId[$parent[$i]['parent']];
			}
			$i++;
		}
		return implode('/', array_reverse(explode('/', $hierarchyUrl)));
	}
	
	public function editTermForm($id){
		return $this->db->getRow('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.id = ?i', $id);
	}
	
	public function edit($title, $url, $content, $parent, $modified, $id){//var_dump($_POST);exit;
		// Обновлям запись
		$this->db->query('UPDATE posts SET title = ?s, url = ?s, content = ?s, parent = ?i, modified = ?s where id = ?i', $title, $url, $content, $parent, $modified, $id);
		$this->editTerms($this->request->post['id'], isset($this->request->post['terms']) ? $this->request->post['terms'] : []);
		
		Msg::json(array('link' => ROOT_URI . ($this->options['rewrite']['slug'] != 'pages' ? $this->options['rewrite']['slug'] . '/' : '') . $this->request->post['url'] . '/'));
	}
	
	// Добавляем новые термины или удаляем ненужные
	public function editTerms($postId, $terms){
		// -Безопасность- Проверим пришедшие термины на валидность. Невалидные отбросим.
		if(empty($terms)) return;
		$terms = $this->checkTermExists($terms);
		
		// Проверяем термины и пишем или удаляем
		// Берем старые термины
		$oldTerms = $this->db->getAll('Select term_taxonomy_id from term_relationships where object_id = ?i', $postId);
		$del = [];
		
		if($oldTerms){
			foreach($oldTerms as $old){
				$find = false;
				foreach($terms as $key => $come){
					// Если в пришедших есть старый, значит с ним ничего делать не надо, исключаем
					if($old['term_taxonomy_id'] == $come){
						unset($terms[$key]);
						$find = true;
						break;
					}
				}
				
				// Если не нашли старый в пришедших, значит этот термин надо удалить
				if(!$find){
					$del[] = $old['term_taxonomy_id'];
				}
			}
		}
		//var_dump($oldTerms, $new);exit;
		// Все пришедшие за исключением старых - новые
		$new = $terms;
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
	
	/**
	 *  Validation come terms
	 *  
	 *  @param array $comeTerms
	 *  
	 *  @return array valid terms id
	 */
	public function checkTermExists($comeTerms){
		$termsId = [];
		if(!empty($comeTerms)){
			// Возьмем все возможные термины(категории и теги) для данного типа поста
			$termsExists = $this->getTermList(array_keys($this->options['taxonomy']));
			
			// merge come terms
			$mergedComeTerms = [];
			foreach($comeTerms as $t){
				$mergedComeTerms = array_merge($mergedComeTerms, $t);
			}
			
			// Проходим по валидным категориям и смотрим есть ли они в пришедших
			// Оставляем лишь валидные
			foreach($termsExists as $term){
				if(in_array($term['id'], $mergedComeTerms)){
					$termsId[] = $term['id'];
				}
			}
		}
		//var_dump($termsId);exit;
		return $termsId;
	}
	
	
	private function changeTermTaxonomyCount($ids, $mark = true){
		$mark = $mark ? '+' : '-';
		$this->db->query("Update term_taxonomy SET count = count {$mark} 1 where term_taxonomy_id IN(" . implode(',', $ids) . ")");
	}
	
	
	// DELETE
	
	public function del($id, $type){
		if($type == 'post') $this->delPost($id);
		else $this->delTerm($id);
		
	}
	
	private function delPost($id){
		if(!$id = $this->db->getOne('Select id, parent from posts where id = ?i', $id))
			exit($this->answers['not_found']);
		
		$this->db->query('Delete from posts where id = ' . $id);
		$this->db->query('Delete from postmeta where post_id = ' . $id);
		$this->db->query('Update posts SET parent = 0 where parent = ' . $id);
		$this->db->query('Update term_taxonomy SET count = count - 1 where term_taxonomy_id IN(Select term_taxonomy_id from term_relationships where object_id = ?i)', $id);
		$this->db->query('Delete from term_relationships where object_id = ' . $id);
		Msg::success();
		
	}
	
	private function delTerm($id){
		if(!$term_taxonomy_id = $this->db->getOne('Select term_taxonomy_id from term_taxonomy as tt, terms as t where tt.term_id = t.id and t.id = ?i',  $id))
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
	
	public function getTermList($taxonomy){
		$terms =  $this->db->getAll('Select DISTINCT t.*, tt.* from terms t, term_taxonomy tt where t.id = tt.term_id and tt.taxonomy IN(\'' . implode("','", (array)$taxonomy) . '\') order by t.id ASC');
		return $terms;
	}
	
	public function getTermsIdByPostId($postId){
		$terms = $this->getTermsByPostId($postId);
		
		$data = [];
		if($terms)
			foreach($terms as $term){
				$data[] = $term['term_id'];
			}
		//var_dump($terms);exit;
		return $data;
	}
	
	public function getTermsByPostId($postId){
		$this->terms = isset($this->terms) ? $this->terms : $this->terms = $this->db->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ?i order by tt.term_id', $postId);
		return $this->terms;
		
	}
	
	public function getFormattedTermList(){
		$data['terms'] = $this->getTermList(array_keys($this->options['taxonomy']));
		return $data;
	}
	
	
	public function checkUrlExists($url, $parent, $id = ''){
		if(is_numeric($id)) $id = ' and id != ' . $id;
		return $this->db->getOne('Select id from posts where url = ?s and parent = ?i and post_type = ?s' . $id, $url, $parent, $this->options['type']) ? true : false;
	}
	
	public function getAllPosts($postType = 'page'){
		$postType = isset($this->options['type']) ? $this->options['type'] : $postType;
		if(!isset($this->allPosts[$postType])){
			$this->allPosts[$postType] = $this->db->getAll('Select * from posts where post_type = ?s order by id', $postType);
		}
		
		return $this->allPosts[$postType];
	}
	
	public function checkExistsPostById($postId){
		if($postId === 0) return true;
		return $this->db->getOne('Select id from posts where id = ?i', $postId);
	}
	
	public function link(){
		
	}
}