<?php

namespace frontend\models\Post;

use Jump\Model;
use Jump\helpers\Filter;
use Jump\helpers\Common;
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
	public $page;
	private $start;
	private $allItemsCount;
	public $select = 'Select * from posts where ';
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	
	private $relationships = 'posts p LEFT JOIN term_relationships tr ON(p.id = tr.object_id) LEFT JOIN term_taxonomy tt ON(tt.term_taxonomy_id = tr.term_taxonomy_id) LEFT JOIN terms t ON(t.id = tt.term_id)';
	
	public function __construct(Taxonomy $taxonomy){
		parent::__construct();
		$this->taxonomy = $taxonomy;
	}
	
	public function single($url, $id = NULL){
		return $id ? $this->getPostById($id) : $this->getPostByUrl($url);
	}
	
	public function getPostById($id){
		return $this->db->getRow($this->select . 'id = ?i', $id);
	}
	
	public function getPostByUrl($url){
		return $this->db->getRow($this->select . 'url = ?s', $url);
	}
	
	public function getChildrens($parentId){
		return $this->db->getAll($this->select . 'parent = ?i', (int)$parentId);
	}
	
	public function getPostTerms($where){
		if(!$where) return false;
		return $this->db->getAll('Select t.*, tt.* from ' . str_replace(['posts p,', 'and p.id = tr.object_id'], '', $this->relationship) . $where);
	}
	
	public function getPostsByPostType($type){
		$query = $this->select . 'post_type = ?s order by created DESC';
		//return $this->db->getAll($query, $type);
		return $this->getAll($query, [$type]);
	}
	
	private function checkTermExists($taxonomy, $value){
		return $this->db->getOne('Select t.name from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.slug = ?s and tt.taxonomy = ?s', $value, $taxonomy);
	}
	
	public function getTermNameByTermSlug($slug){
		return $this->db->getOne('Select name from terms where slug = ?s LIMIT 1', $slug);
	}
	
	public function getPostsBysTermsTaxonomyIds($termsTaxonomyIds){
		$query = 'Select distinct p.* from ' . $this->relationship . 'and tt.term_taxonomy_id IN(?a) group by p.id order by p.created DESC';
		$countCache = $this->db->getAll(str_replace('distinct p.*', 'count(distinct p.id) as count', $query), [$termsTaxonomyIds]);
		$count = 0;
		if($countCache){
			foreach($countCache as $c)
				$count += $c['count'];
		}
		return $this->getAll($query, [$termsTaxonomyIds], true, $count);
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
	
	private function checkInLimit($query, $params, $count){
		$this->allItemsCount = $count ?: (int)call_user_func_array([$this->db, 'getOne'], array_merge([str_replace('Select *', 'Select COUNT(*) as count', $query)], $params));
		
		if($this->allItemsCount && $this->allItemsCount <= $this->start){
			$this->request->location(preg_replace('~page/\d+/?~', '', FULL_URL));
		}
	}
	
	private function getAll($query, $params, $array = false, $count = null){
		if($array) $params = [$params];
		$this->checkInLimit($query, $params, $count);
		$data = call_user_func_array([$this->db, 'getAll'], array_merge([$query . $this->limit], $params));
		return $data;
	}
	
	public function getAllItemsCount(){
		return $this->allItemsCount;
	}
	
	public function getFiltersHTML($taxonomies, $postType, $postSlug){
		if(!$taxonomies) return false;
		if(!$terms = $this->db->getAll('Select DISTINCT t.*, tt.* from ' . $this->relationship . ' and p.post_type = \''.$postType.'\' and tt.taxonomy IN(\'' . implode("','", $taxonomies) . '\')')) return false;
		
		return $this->getFiltersHTMLForList($terms, $postSlug);
	}
	
	private function getFiltersHTMLForList($terms, $postSlug){
		$tmpTaxonomy = $terms[0]['taxonomy'];
		foreach($terms as $key => $term){
			$key1 = Options::get('taxonomy')[$term['taxonomy']]['title'];
			if(!isset($html[$key1])) $html[$key1] = '';
			$link = SITE_URL . Options::getArchiveSlug() . "{$term['taxonomy']}/{$term['slug']}/";
			$html[$key1] .= $this->setTermLinkHelper($link, $term['name'], $term['count']) . '<br>';
		}
		foreach($html as $tax => $h) 
			$html[$tax] = '<div class="filters"><div class="title">' . $tax . '</div><div class="content">' . $h . '</div></div>';
		
		return implode('', array_merge(['all' => '<a href="'. SITE_URL . Options::getArchiveSlug() . '">Все</a><br>'], $html));
	}
	
	
	public function getTermListByPostId($terms){
		
		//if(!$terms = $this->getTaxonomies($postId)) return false;
		$html = [];
		if($terms){
			foreach($terms as $key => $term){
				if(!isset($html[$term['taxonomy']])) $html[$term['taxonomy']] = ($key ? '<br>' : '') . Options::get('taxonomy')[$term['taxonomy']]['title'] . ': ';
				$html[$term['taxonomy']] .= "<a href='" . SITE_URL . Options::getArchiveSlug() . "{$term['taxonomy']}/{$term['slug']}/'>{$term['name']}</a>, ";
			}
			foreach($html as &$h) 
				$h = substr($h, 0, -2);
		}
		return implode('', $html);
	}
	
	public function getTaxonomies($postId){
		if(!Options::get('taxonomy')) return false;
		$this->terms = isset($this->terms) ? $this->terms : $this->db->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ' . $postId . ' and tt.taxonomy IN(\'' . implode("','", array_keys(Options::get('taxonomy'))) . '\')');
		return $this->terms;
	}
	
	
	public function getTermsListByTaxonomy($taxonomy, $delimiter = false, $getCount = true){
		if(!$terms = $this->db->getAll('Select DISTINCT t.*, tt.* from ' . $this->relationship . ' and p.post_type = \''.Options::get('type').'\' and tt.taxonomy = \''.$taxonomy.'\'')) return false;
		$allCount = 0;
		foreach($terms as $t){
			if(!$getCount) $t['count'] = 0;
			$allCount += $t['count'];
			$html[] = $this->setTermLinkHelper(SITE_URL . Options::getArchiveSlug() . "{$t['taxonomy']}/{$t['slug']}/", $t['name'], $t['count']);
		}
		$result = array_merge([$this->setTermLinkHelper(SITE_URL . Options::getArchiveSlug(), 'all')], $html);
		return ($delimiter && is_string($delimiter)) ? (Options::get('taxonomy')[$taxonomy]['title'] . ': ' . implode($delimiter, $result)) : $result;
	}
	
	private function setTermLinkHelper($link, $text, $count = 0){
		$count = $count ? " ({$count})" : '';
		return urldecode(FULL_URL_WITHOUT_PARAMS) == $link ? "<span style='border-bottom: 3px #de1d1d solid;'>{$text}{$count}</span>" : "<a href='{$link}'>{$text}</a>{$count}";
	}
	
	public function getArchiveSlug(){
		return Options::get('has_archive') . (Options::get('has_archive') ? '/' : '');
	}
	
	public function getPostsByTermNames($termNames){
		return $this->db->getAll('Select DISTINCT p.* from ' . $this->relationship . ' and t.name IN(?a)', [$termNames]);
	}
	
	public function getPostsByTaxonomyAndPostType($taxonomy, $postType){
		return $this->db->getAll('Select DISTINCT p.* from ' . $this->relationship . ' and tt.taxonomy = ?s and p.post_type = ?s', $taxonomy, $postType);
	}
	
	public function getComments($postId){
		$generalComments = $this->db->getAll('Select * from comments where comment_post_id = ' . $postId . ' AND comment_parent = 0 order by comment_date DESC LIMIT 20');
		if(!$generalComments) return false;
		
		$ids = Common::getKeys($generalComments, 'comment_id');
		
		$subComments = $this->db->getAll('Select * from comments where comment_post_id = ' . $postId . ' AND comment_parent IN(\''.implode("','", $ids).'\') order by comment_date DESC');
		
		$commentsCount = $this->db->getOne('Select COUNT(*) as count from comments where comment_post_id = ' . $postId);
		
		if($subComments)
			$subComments = Common::itemsOnKeys($subComments, ['comment_parent']);
		return ['general' => $generalComments, 'sub' => $subComments, 'count' => $commentsCount];
	}
}