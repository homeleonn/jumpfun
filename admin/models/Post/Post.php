<?php

namespace admin\models\Post;

use Jump\Model;
use Jump\helpers\Msg;
use Jump\helpers\MyDate;
use Jump\helpers\Common;
use Jump\traits\PostTrait;
use Jump\DI\DI;
use Jump\core\cache\Cache;
use frontend\models\Post\Taxonomy;
use frontend\models\Post\Options;

class Post extends Model{
	use PostTrait;
	private $allPosts;
	private $answers = [
		'not_found' => 'Произошла ошибка: Возможно данной записи не существует'
	];
	private $select = 'Select * from posts where ';
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	
	public function __construct(DI $di, Taxonomy $taxonomy){
		parent::__construct($di);
		$this->taxonomy = $taxonomy;
	}
	
	public function postList(){
		// Get all posts
		if(!$posts = $this->getAllPosts($this->options['type'], ['id', 'parent', 'title', 'url', 'created'])) return false;
		
		$addKeys = [];
		if(!$this->options['hierarchical']){
			// Get posts terms
			foreach($posts as $post) $ids[] = $post['id'];
			$addKeys['_terms'] = $this->getTermsByPostsId($ids);
			Cache::set('postTerms', $addKeys['_terms']);
			
			// Get all terms for post taxonomies for cache(build link)
			Cache::set('allTerms', $this->taxonomy->getByTaxonomies());
		}
		
		
		// build hierarchy
		$postsHierarchy = $this->hierarchyItems($posts, NULL, NULL, $addKeys);
		$postsTable = $this->hierarchy($postsHierarchy, 'table');
		return !$postsTable ? '' : '<table class="mytable"><tr align="center"><td>title/url</td>'.($this->options['taxonomy'] ? '<td width="10%">Метки</td>' : '').'<td width="1%">Дата публикации</td></tr>' . $postsTable . '</table>';
	}
	
	public function termList($term){
		$terms = $this->getTermList($term);
		$termsHierarchy = $this->hierarchyItems($terms);
		$termsTable = '<table class="mytable"><tr align="center"><td>Заголовок</td><td width="1%">Кол-во</td></tr>' . $this->hierarchy($termsHierarchy, 'table') . '</table>';//echo($termsTable);exit;
		return $termsTable;
	}
	
	// ADD
	
	public function addForm(){
		$data = [];
		
		if($this->options['hierarchical']){
			$posts = $this->getAllPosts($this->options['type'], ['id', 'parent', 'title', 'url']);
			$itemsToParents = $this->hierarchyItems($posts);
			$data['listForParents'] = '<select style="width: 100%;"name="parent"><option value="0">(нет родительской)</option>' . $this->hierarchy($itemsToParents) . '</select>';
		}elseif($this->options['taxonomy']){
			$data['terms'] = $this->getTermList(array_keys($this->options['taxonomy']));
			$data['terms'] = $this->hierarchyItems($data['terms']);
		}
			
		return $data;
	}
	
	/**
	 *  Return items like hierarchy
	 *  
	 *  @param int $selfId item id for which returns parents
	 *  @param int $parent item parent
	 */
	private function hierarchyItems($items, $selfId = NULL, $parent = NULL, $addKeys = []){
		$isTerm = isset($items[0]['taxonomy']);
		foreach($items as $item){
			if($item['id'] == $selfId) continue;
			if(!empty($addKeys)){
				foreach($addKeys as $key => $values){
					if(!$values) break;
					if(isset($addKeys[$key][$item['id']])){
						$item[$key] = $addKeys[$key][$item['id']];
						unset($addKeys[$key][$item['id']]);
					}else{
						$item[$key] = [];
					}
				}
			}
			$itemsToParents[$item['parent']][] = $item;
		}
		ksort($itemsToParents);
		$itemsToParents = array_reverse($itemsToParents, true);
		if($this->options['hierarchical'] || $isTerm){
			foreach($itemsToParents as &$items){
				foreach($items as &$item){
					if(isset($itemsToParents[$item['id']])){
						$item['children'] = $itemsToParents[$item['id']];
						unset($itemsToParents[$item['id']]);
					}
				}
			}
		}
		
		return $itemsToParents[0];
	}
	
	private function getTermsByPostsId($ids){//var_dump($ids);
		$ids = is_array($ids) ? implode(',', $ids) : $ids;
		if(!$terms = $this->db->getAll('Select t.*, tt.*, tr.object_id from terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tr.term_taxonomy_id = tt.term_taxonomy_id and object_id IN('.$ids.') order by t.id ASC')) return false;
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
	private function hierarchy($items, $type = 'select', $parent = 0, $level = 0, $urlHierarchy = ''){
		$html = '';
		foreach($items as $item){
			if($type == 'select'){
				$title = isset($item['title']) ? $item['title'] : $item['slug'];
				$html .= '<option '.($item['id'] == $parent ? 'selected' : '').' value="' . $item['id'] . '">' . str_repeat('&nbsp;', $level * 3) . ($level ? '&#8735;'  : '') . (mb_strlen($title) > 46 ? mb_substr($title, 0, 45) . '...' : $title) . '</option>';
			}elseif($type = 'table'){
				$html .= $this->hierarchyListHtml($item, $level, $urlHierarchy);
			}
			
			if(isset($item['children'])){
				$urlHierarchy .= isset($item['url']) ? $item['url'] : $item['slug'] . '/';
				$html .= $this->hierarchy($item['children'], $type, $parent, $level + 1, $urlHierarchy);
			}
			
			if(!$item['parent'])
				$urlHierarchy = '';
		}
		return $html;
	}
	
	private function hierarchyListHtml($item, $level, $urlHierarchy){
		$isPost = !isset($item['taxonomy']);
		if($isPost && !$this->options['hierarchical']){
			list($termsOnId, $termsOnParent) = Common::itemsOnKeys(getTermsByTaxonomies(), ['id', 'parent']);
			$termsByPostId = getTermsByPostId($item['id']);
			$permalink 	 = SITE_URL . trim(Options::slug(), '/') . '/' . $item['url'] . '/';
			$item['url'] = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
		}
		
		if($isPost){
			$url = $this->options['hierarchical'] ? ROOT_URI . $urlHierarchy . \Options::getArchiveSlug() . $item['url'] . '/' : $item['url'];
		}else{
			$url = ROOT_URI . \Options::getArchiveSlug() . $item['taxonomy'] . '/' . $urlHierarchy . $item['slug'] . '/' ;
		}
		
		
		$link = '<a href="' . $url . '">перейти</a>';
		$edit = '<a href="' . SITE_URL . 'admin/' . $this->options['type'] . '/' . ($isPost ? 'edit' : 'edit-term') . '/' . $item['id'] . '/">%s</a>';
		ob_start();
		?>
			<tr>
				<td class="admin-page-list">
					<?=str_repeat('&mdash;', $level) . ' ' . sprintf($edit, $item[$isPost ? 'title' : 'name']);?>
					<div style="position: absolute;">
						[<?=$link;?>]
						[<a href="#">свойства</a>]
						[<?=sprintf($edit, 'изменить');?>]
						[<a style="color: red;" href="javascript:void(0);" title="<?=$this->options['delete']?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$this->options['type']?>',<?=$item['id'];?>, '<?=($isPost ? 'post' : 'term')?>');">удалить</span></a>]
					</div>
				</td>
				<?php 
					if($this->options['taxonomy'] && $isPost){
						if(!isset($item['_terms']))echo '<td></td>';
						else{
							$activeTaxonomy = array_keys($this->options['taxonomy']);
							echo '<td>';ob_start();
							foreach($item['_terms'] as $term){
								if(!in_array($term['taxonomy'], $activeTaxonomy)) continue;
								echo '<a href="'. SITE_URL . $this->options['rewrite']['slug'] . '/' . $term['taxonomy'] . '/' . $term['slug'] . '/">'.$term['name'].'</a>, ';
							}
							echo substr(ob_get_clean(), 0, -2) . '</td>';
						}
					}
					if(isset($item['add_keys'])){
						foreach($item['add_keys'] as $value)
							echo '<td>'.$value.'</td>';
					}
				?>
				<td><?=($isPost ? $item['created'] : $item['count']);?></td>
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
		$terms = $this->getAllTerms(array_keys($this->options['taxonomy']));
		$itemsToParents = $this->hierarchyItems($terms);
		$data['listForParents'] = '<select style="width: 100%;"name="parent"><option value="0">(нет родительской)</option>' . $this->hierarchy($itemsToParents) . '</select>';
		return $data;
	}
	
	public function addTerm($name, $term, $whisper = false, $slug = '', $description = '', $parent = 0){
		return $this->addTermHelper($name, $term, $whisper, $slug, $description, $parent );
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
	
	
	
	public function addTermHelper($name, $term, $whisper = false, $slug = '', $description = '', $parent = 0){
		// Checking on duplicate term for this taxonomy
		$duplicate = $this->db->getOne('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = \'' . $term .'\'', $name);
		
		if($duplicate){
			if($whisper) return false;
			exit;
		}
		
		$parent = $this->db->getOne('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and t.id = ?i', $parent);
		
		if(!$parent){
			if($whisper) return false;
			exit;
		}
		
		// Add new term and taxonomy
		if($result = $this->db->query('INSERT INTO terms (name, slug) VALUES (?s, ?s)', $name, $slug ?: $name)){
			$result = $this->db->query('INSERT INTO term_taxonomy (term_id, taxonomy, description, parent) VALUES (?s, ?s, ?s, ?i)', $this->db->insertId(), $term, $description, $parent);
		}
		
		if($whisper) return $result;
		if($result)  echo 1;
		exit;
	}
	
	
	// EDIT
	
	public function editForm($id){
		if(!$post = $this->db->getRow('Select * from posts where id = ?i', $id)) return 0;
		if($this->options['hierarchical']){
			$post['urlHierarchy'] = $this->getUrlHierarchy($post['id']);
			$posts = $this->getAllPosts($this->options['type'], ['id', 'parent', 'title', 'url']);
			$itemsToParents = $this->hierarchyItems($posts, $post['id']);
			$post['listForParents'] = '<select style="width: 100%;"name="parent"><option value="0">(нет родительской)</option>' . $this->hierarchy($itemsToParents, 'select', $post['parent']) . '</select>';
			$post['anchor'] = SITE_URL . $post['urlHierarchy'];
			$post['permalink'] = $post['anchor'] . $post['url'] . '/';
		}
		
		else{
			$termsByPostId = $this->getTermsByPostsId($id)[$id];
			
			// terms id of this post for checkbox checked
			if($termsByPostId)
				foreach($termsByPostId as $t) $post['selfTerms'][] = $t['term_id'];
			
			$post['terms'] = $this->taxonomy->getByTaxonomies();
			Cache::set('allTerms', $post['terms']);
			list($termsOnId, $termsOnParent) = Common::itemsOnKeys(getTermsByTaxonomies(), ['id', 'parent']);
			$permalink 	 = SITE_URL . trim(Options::slug(), '/') . '/' . $post['url'] . '/';
			$post['permalink'] = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
			$post['anchor'] = str_replace($post['url'] . '/', '', $post['permalink']);
		}
		if($post['terms'])
			$post['terms'] = $this->hierarchyItems($post['terms']);
		
		return $post;
		//return !isset($formattedTermList) ? $post : array_merge($post, $formattedTermList);
	}
	
	public function getUrlHierarchy($childId){
		$posts = $this->getAllPosts($this->options['type']);
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
		$data['term'] = $this->db->getRow('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.id = ?i', $id);
		$terms = $this->getAllTerms(array_keys($this->options['taxonomy']));
		$itemsToParents = $this->hierarchyItems($terms, $data['term']['id']);
		$data['listForParents'] = '<select style="width: 100%;"name="parent"><option value="0">(нет родительской)</option>' . $this->hierarchy($itemsToParents, 'select', $data['term']['parent']) . '</select>';
		return $data;
		
	}
	
	public function edit($title, $url, $content, $parent, $modified, $id){//var_dump($_POST);exit;
		// Обновлям запись
		$this->db->query('UPDATE posts SET title = ?s, url = ?s, content = ?s, parent = ?i, modified = ?s where id = ?i', $title, $url, $content, $parent, $modified, $id);
		$this->editTerms($this->request->post['id'], isset($this->request->post['terms']) ? $this->request->post['terms'] : []);
		
		Msg::json(1);
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
	
	public function editTerm($id, $name, $slug, $description, $parent){
		$this->db->query("Update terms SET name = ?s, slug = ?s where id = ?i", $name, $slug, $id);
		$this->db->query("Update term_taxonomy SET description = ?s, parent = ?i where term_id = ?i", $description, $parent, $id);
		$this->request->location(FULL_URL . '?msg=успешно');
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
	
	
	public function getTermsByPostId($postId){
		var_dump(Cache::get('postTerms'), $postId);exit;
		$terms = Cache::get('postTerms') !== NULL ? Cache::get('postTerms') : $this->db->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ?i order by tt.term_id', $postId);
		Cache::set('postTerms', $terms);
		return $terms;
		
	}
	
	
	
	public function checkUrlExists($url, $parent, $id = ''){
		if(is_numeric($id)) $id = ' and id != ' . $id;
		return $this->db->getOne('Select id from posts where url = ?s and parent = ?i and post_type = ?s' . $id, $url, $parent, $this->options['type']) ? true : false;
	}
	
	public function getAllPosts($postType, $columns = []){
		$key = empty($columns) ? '*' : implode(',', $columns); 
		if(!isset($this->allPosts[$postType][$key])){
			$this->allPosts[$postType][$key] = $this->db->getAll('Select ' . $key . ' from posts where post_type = ?s order by id', $postType);
		}
		
		return $this->allPosts[$postType][$key];
	}
	
	public function getAllTerms($taxonomies){
		$taxonomies = implode("','", $taxonomies);
		if(!isset($this->allTerms[$taxonomies])){
			$this->allTerms[$taxonomies] = $this->db->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_taxonomy_id and tt.taxonomy IN(\''.$taxonomies.'\')');
		}
		
		return $this->allTerms[$taxonomies];
	}
	
	
	
	public function checkExistsPostById($postId){
		if($postId === 0) return true;
		return $this->db->getOne('Select id from posts where id = ?i', $postId);
	}
}