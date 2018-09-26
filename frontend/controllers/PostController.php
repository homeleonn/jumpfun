<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Filter;
use Jump\helpers\Common;
use Jump\helpers\Pagenation;
use frontend\models\Post\Options;

class PostController extends Controller{
	use \Jump\traits\PostControllerTrait;
	
	private $isFront = false;
	private $img = '_jmp_post_img';
	
	public function __construct($postType = false){
		parent::__construct();
		$this->setOptions($postType);
	}
	
	private function setOptions($postType = false){
		$this->options = $this->config->getCurrentPageOptions($postType);
		Options::setOptions($this->options);
	}
	
	public function actionIndex(){
		$frontPage = $this->config->front_page;
		if(is_numeric($frontPage)){
			return $this->actionSingle(NULL, $frontPage);
		}
		else{
			return $this->last();
		}
	}
	
	public function last(){
		//dd($this->view->is('front'));
		$this->view->is('front');
		$this->isFront = true;
		$this->config->setOption('postType', 'post');
		$this->setOptions();
		return $this->actionList(NULL, NULL, $this->page);
	}
	
	private function checkFront($url){
		if(preg_match('~page/(\d+)~', $url, $matches) && $this->config->front_page == 'last'){
			$this->page = $matches[1];
			return $this->last();
		}
		return false;
	}
	
	
	/**
	 *  @param string $url (.*)
	 *  @param int $id
	 *  
	 *  @return array post
	 */
	public function actionSingle($url, $id = NULL){
		global $funkidsFileCacheName;
		$funkidsFileCacheName = (is_null($id) ? md5($url) : ($id == getOption('front_page') ? $id : ''));
		if($funkidsFileCacheName != ''){
			//if(!is_null($id))
				if(Common::getCache($funkidsFileCacheName = 'pages/' . $funkidsFileCacheName, -1) !== FALSE){$this->view->rendered = true;return;}	
		}
		
		if($data = $this->checkFront($url))
			return $data;
		
		// get all args, but may be come 'foo/bar/baz/zab/'
		$hierarchy = explode('/', $url);
		
		//The validation of each section of the hierarchy
		if($url && count($hierarchy) > 1){
			if(in_array('', $hierarchy)) $this->request->notFound();
			// foreach($hierarchy as $url) 
				// if(!preg_match('~^'.URL_PATTERN.'$~u', $url)) $this->request->location(NULL, 404);
			$id = NULL;
		}
		
		// get last component from url(url in db), find in posts with this url and if this a front page - return
		$url = array_pop($hierarchy);
		
		//echo "Запрос записи из базы(урл: $url, айди: $id)...<br>";
		if(!$post = $this->model->single($url, $id)) return 0;
		$post['__model'] = $this->model;
		
		// Set post type and type options
		$this->config->setOption('postType', $post['post_type']);
		$this->setOptions();
		$post = $this->model->mergePostMeta($post, true);
		
		// If this post is the front
		if($post['id'] == $this->config->front_page){
			if(SITE_URL != FULL_URL_WITHOUT_PARAMS) $this->request->location(SITE_URL, 301);
			return $post;
		}
		
		// If type of this post related taxonomy
		if(!$this->options['hierarchical']){
			$post = $this->taxonomyPost($post);
		}
		// If type of this post is hierarchical structure, check hierarchy
		else{
			//if(!empty($hierarchy))
				$this->checkHierarchy($post['url'], $post['parent'], $hierarchy);
		}
		
		$this->addBreadCrumbs($post);
		if(!isset($post['__model'])) $post['__model'] = $this->model;
		//dd($post);
		// if($post['comment_status'] == 'open')
		// $post['comments'] = $this->model->getComments($post['id']);
		return $post;
	}
	
	/**
	 *  Запись связанная таксономией
	 *  Строит правильную ссылку, опираясь на пренадлежность к терминам и проверяем с пришедшей
	 *  Строит html список терминов, к котором пренадлежит запись
	 *  
	 *  @param type $post
	 *  
	 *  @return post
	 */
	private function taxonomyPost($post)
	{
		// What are the taxonomies of the post
		$postTypeTaxonomies = array_keys(Options::get('taxonomy'));
		if(empty($postTypeTaxonomies)){
			$termsOnId = $termsOnParent = $postTerms = [];
			
			$post['terms'] = NULL;
		}else{
			// Get terms by post taxonomies
			$terms = $this->model->taxonomy->getByTaxonomies(); 
			
			// Получим термины относящиеся к данной записи, которые привязаны к таксономиям данного типа записи
			$postTerms = $this->model->getPostTerms(' and tr.object_id = ' . $post['id'] . ' and tt.taxonomy IN(\''.implode("','", $postTypeTaxonomies).'\')');
			
			// Сгрупируем все термины данных таксономий по айди и родителю
			list($termsOnId, $termsOnParent) = !Common::itemsOnKeys($terms, ['id', 'parent']);
			
			// Получим термины в виде списка html
			$post['terms'] = Common::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $postTerms), Options::getArchiveSlug());
		}
		
		// Сформируем полную ссылку на пост, учитывая иерархию терминов к которым принадлежит запись
		$this->postPermalink($post, $termsOnId, $termsOnParent, $postTerms);
		
		// Если правильная ссылка на запись и пришедшая не совпадают - отправляем по правильному адресу
		if(\langUrl(FULL_URL) != $post['url']){
			$this->request->location($post['url']);
			//dd('$this->request->location('.$post['url'].')');
		}
		
		// Указываем что выводить данную запись следует шаблоном single
		$this->view->is('single');
		$post = applyFilter('before_return_post', $post);
		
		return $post;
	}
	
	private function checkHierarchy($url, $parent, $hierarchy){//dd(func_get_args());
		if(!$parent){
			if($hierarchy)
				$this->request->location(NULL, 404);
			return false;
		}else{
			if(!$hierarchy){
				// взять все страницы, создать иерархию и перенаправить
				//dd($this->model->getPostsByPostType('page') , $parent, 'url');
				$this->request->location(SITE_URL . $this->getParentHierarchy($parent, $this->model->getPostsByPostType('page'),  'url') . '/' . $url . '/', 301);
			}else{
				$parents = $this->db->getAll('Select id, title, short_title, url, parent from posts where url IN(\''.implode("','", $hierarchy).'\') order by parent DESC');
				//d($parents, $hierarchy, $parent);
				if(count($parents) < count($hierarchy)){
					$this->request->location(NULL, 404);
				}else{
					$h = array_reverse($hierarchy);
					$tempParent = $parent;
					$i = 0;
					$addBreadCrumbs = [];
					
					foreach($parents as $parent){//d($parent['id']);
						if($parent['id'] != $tempParent || $parent['url'] != $h[$i]){
							$this->request->location(NULL, 404);
						}
						$tempParent = $parent['parent'];
						$addBreadCrumbs[$h[$i]] = $parent['short_title'] ?: $parent['title'];
						$i++;
					}
					if($tempParent)
						$this->request->location(NULL, 404);
					
					foreach(array_reverse($addBreadCrumbs) as $link => $title){
						$this->config->addBreadCrumbs($link, $title);
					}
				}
			}
		}
	}
	
	private function checkTermHierarchy($postId, $hierarchy){
		$term = $this->model->getPostTerms('and p.id = ' . $postId . ' and t.slug IN(\''.implode("','", [end($hierarchy)]).'\')');
		if(!$term)
			$this->request->location(null, 404);
		if(!$term[0]['parent']){
			if(count($hierarchy) > 1)
				$this->request->location(null, 404);
		}else{
			// определить валидную иерархию **-
			$validTerms = $this->model->getTaxonomies($postId);
			$urlHierarchy = implode('/', $hierarchy);
			$validUrlHierarchy = $this->getParentHierarchy($term[0]['parent'], $validTerms, 'slug') . '/' . end($hierarchy);
			if($validUrlHierarchy != $urlHierarchy){
				$this->request->location(str_replace($urlHierarchy, $validUrlHierarchy , FULL_URL), 301);
			}
		}
	}
	
	private function getParentHierarchy($parentId, $items, $compare){
		foreach($items as $item){
			$itemsOnId[$item['id']] = $item;
		}
		$hierarchy = $this->setHierarchy($itemsOnId, $parentId, $compare);
		$hierarchy = implode('/', array_reverse(explode('|', substr($hierarchy, 0, -1))));
		return $hierarchy;
	}
	
	private function setHierarchy($items, $parentId, $compare){
		if(!isset($items[$parentId])) return false;
		$hierarchy = $items[$parentId][$compare] . '|';
		if(isset($items[$parentId]['parent']) && $items[$parentId]['parent']) 
			$hierarchy .= $this->setHierarchy($items, $items[$parentId]['parent'], $compare);
		return $hierarchy;
	}
	
	public function actionList($taxonomy = null, $taxonomySlug = null, $page = 1, $limit = false, $orderBy = false){//dd(func_get_args(), $this->options);
		global $thatCache;//dd($thatCache);
		if($taxonomy == '' && $taxonomySlug == '' && !$thatCache){
			global $funkidsFileCacheName;
			$funkidsFileCacheName = 'pages/list-' . $this->options['type'];
			if(Common::getCache($funkidsFileCacheName, -1) !== FALSE){$this->view->rendered = true;return;}
		}
		//$this->model->setLimit($this->page = $page, $this->perPage);
		//d($this->options['rewrite']);
		$this->model->setLimit($this->page = $page, ($limit && is_numeric($limit)) ? $limit : $this->options['rewrite']['paged']);
		$list = $this->options;
		if($this->page > 1){
			$list['title'] .= ' | Страница ' . $this->model->page;
		}
		$listMark = '__list';
		
		$hierarchy = explode('/', $taxonomySlug);
		
		// Если не пришла таксономия и у данного типа поста есть архив -  выдаем просто весь архив
		if(!$taxonomy){
			//if($this->options['has_archive']){
				if(!$list[$listMark] = $this->model->getPostsByPostType(Options::get('type'), $orderBy)) return 0;
				$terms = !empty(Options::get('taxonomy')) ? $this->model->taxonomy->getAllByObjectsIds(array_keys(Common::itemsOnKeys($list[$listMark], ['id']))) : [];
				//dd(array_keys(Common::itemsOnKeys($list[$listMark], ['id'])), $list[$listMark], $terms);
				
				$termsByPostId = $terms ? Common::itemsOnKeys($terms, ['object_id']) : [];
				$terms1 = $this->model->taxonomy->getByTaxonomies();
			// }else{
				// dd(1);
			// }
			//dd($list[$listMark], $terms1, $termsByPostId);
			
		}else{
			// taxonomy validation
			if(!$this->isFront && !Common::checkValidation($hierarchy, '/^' . URL_PATTERN . '$/')){
				$this->request->location(null, 404);
				//exit('404-5 - taxonomy validation failed by URL_PATTERN');
			}
			
			
			// 1. проверить иерархию таксономий
			// 2. найти всех потомков
			// 3. взять все записи связанные с потомками включая текущий термин
			// 4. выводить записи, с урлами отфильтрованными в зависимости от slug
			
			// 1
				// 1.1 возьмем все термины данной таксы
				// 1.2 проверим валидность последнего термина
				// 1.3 строим иерархию к первому родителю, компонуем потомков, сверяем иерархию с пришедшей
			
			// 1.1
			$terms1 = $this->model->taxonomy->getByTaxonomies();
			$terms  = $this->model->taxonomy->filter($terms1, 'taxonomy', $taxonomy);
			
			// 1.2
			$lastChild = $hierarchy[count($hierarchy) - 1];
			$findTerm = false;
			// get current selected term to know whence build hierarchy
			
			foreach($terms as $term){
				if($term['slug'] == $lastChild){
					$currentTerm = $term;
					$findTerm = true;
					break;
				}
			}
			if(!$findTerm) {
				$this->request->location(null, 404);
				//exit('404-3');
			}
			
			// 1.3
			list($termsOnIds, $termsOnParents) = Common::itemsOnKeys($terms, ['id', 'parent']);
			$builtedTermsParentHierarchy = substr(str_replace('|', '/', Common::builtHierarchyDown($termsOnIds, $currentTerm, 'slug') . '|' .$lastChild), 1);
			
			if(implode('/', $hierarchy) != $builtedTermsParentHierarchy) 
				exit('location: ' . $builtedTermsParentHierarchy);
			
			// 2 // 3
			// Создаем список всех дочерних позиций всей иерархии терминов относительно текущего термина
			$toShow = isset($termsOnParents[$currentTerm['id']]) ? $termsOnParents[$currentTerm['id']] : NULL;
			$i = 0;
			$termsTaxonomyIds[] = $currentTerm['term_taxonomy_id'];
			
			while(isset($toShow[$i])){
				$termsTaxonomyIds[] = $toShow[$i]['term_taxonomy_id'];
				if(isset($termsOnParents[$toShow[$i]['id']])){
					$toShow = array_merge($toShow, $termsOnParents[$toShow[$i]['id']]);
				}
				$i++;
			}
			
			if(!$list[$listMark] = $this->model->getPostsBysTermsTaxonomyIds($termsTaxonomyIds, $orderBy)) 
				return $this->options;
			
			$terms = $this->model->taxonomy->getAllByObjectsIds(array_keys(Common::itemsOnKeys($list[$listMark], ['id'])));
			
			$termsByPostId = Common::itemsOnKeys($terms, ['object_id']);
			// 4
				// 4.1 взять первый термин данного поста
				// 4.2 взять все термины по таксе выбранного термина из пункта 4.1
				// 4.3 составить иерархию с помощью хелпера common
		}
		
		list($termsOnId, $termsOnParent) = !Common::itemsOnKeys($terms1, ['id', 'parent']);
		foreach($list[$listMark] as &$post){
			if(!isset($termsByPostId[$post['id']])) $termsByPostId[$post['id']] = false;
			$this->postPermalink($post, $termsOnId, $termsOnParent, isset($termsByPostId[$post['id']])?$termsByPostId[$post['id']]:false);
			$post['terms'] = Common::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $termsByPostId[$post['id']]), Options::getArchiveSlug());
		}
		
		
		
		// Узнаем имя таксономии по метке для хлебных крошек
		$taxonomyName = [];
		
		foreach($hierarchy as $section){
			foreach($terms1 as $term){
				if($term['slug'] == $section){
					$taxonomyName[] = $term['name'];
					break;
				}
			}
		}
		if(empty($taxonomyName))
			$taxonomyName = $taxonomySlug;
		
		$taxonomyTitle = $taxonomy ? $this->options['taxonomy'][$taxonomy]['title'] : '';
		
		$this->addBreadCrumbs($list, $taxonomyTitle, $taxonomyName, $taxonomyName);
		
		$list['pagenation'] = $this->pagination();
		
		//dd($list);
		$archiveTerms = $terms1;
		
		list($termsOnId, $termsOnParent) = !Common::itemsOnKeys($archiveTerms, ['id', 'parent']);
		$postTerms = $this->postTermsLink($termsOnId, $termsOnParent, $archiveTerms);
		
		$list[$listMark] = $this->fillMeta($list[$listMark]);
		
		if($postTerms)
			$list['filters'] = Common::archiveTermsHTML(array_reverse($postTerms), Options::getArchiveSlug());
		$list['__model'] = $this->model;
		$this->view->is('list');
		if($this->isFront){
			$list['title'] = Common::getOption('title');
			$list['description'] = Common::getOption('description');
		}
		$list[$listMark] = applyFilter('before_return_post', $list[$listMark]);
		return $list;
	}
	
	private function fillMeta($posts){
		$postsOnId = Common::itemsOnKeys($posts, ['id']);
		//dd($posts, $postsOnId);
		$ids = array_keys($postsOnId);
		//dd($ids);
		
		$meta = $this->db->getAll('Select post_id, meta_key, meta_value from postmeta where post_id IN('.implode(',', $ids).')');
		//$comments = $this->db->getAll('Select comment_post_id from comments where comment_post_id IN('.implode(',', $ids).')');
		$comments = [];
		
		$commnetsOnId = $comments ? Common::itemsOnKeys($comments, ['comment_post_id']) : [];
		if($meta){
			$posts = $mediaIds = [];
			foreach($meta as $m){
				if($m['meta_key'] == $this->img)
					$mediaIds[$m['post_id']] = $m['meta_value'];
				$postsOnId[$m['post_id']][0][$m['meta_key']] = $m['meta_value'];
			}
			
			if(!empty($mediaIds)){
				$media = $this->db->getAll('Select * from media where id IN ('.implode(',', $mediaIds).')');
				$mediaOnId = Common::itemsOnKeys($media, ['id']);
				foreach($mediaIds as $postId => $mediaId){
					$postsOnId[$postId][0][$this->img] = $mediaOnId[$mediaId][0]['src'];
					$postsOnId[$postId][0][$this->img . '_meta'] = unserialize($mediaOnId[$mediaId][0]['meta']);
				}
			}
			
			
			foreach($postsOnId as $post){
				$post = $post[0];
				$post['comment_count'] = isset($commnetsOnId[$post['id']]) ? count($commnetsOnId[$post['id']]) : 0;
				$posts[] = $post;
			}
		}
		return $posts;
	}
	
	
	public function postPermalink(&$post, $termsOnId, $termsOnParent, $termsByPostId, $slug = false){//var_dump(func_get_args());exit;
		$permalink 	 = SITE_URL . langUrl() . ($slug ?: trim(Options::slug(), '/')) . '/' . $post['url'] . '/';
		$post['url'] = $post['permalink'] = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
	}
	
	private function pagination(){
		//dd($this->page, $this->model->getAllItemsCount(), $this->perPage);
		return (new Pagenation())->run($this->model->page, $this->model->getAllItemsCount(), $this->options['rewrite']['paged']);
		//return (new Pagenation())->run($this->model->page, $this->model->getAllItemsCount(), $this->perPage);
	}
	
	private function postTermsLink($termsOnId, $termsOnParent, $termsByPostId, $mergeKey = 'slug'){//var_dump(func_get_args());exit;
		if(!$termsByPostId) return;
		foreach($termsByPostId as $postTerm){
			$title = Options::get('taxonomy')[$postTerm['taxonomy']]['title'];
			if(!isset($postTerms[$title])) $postTerms[$title] = [];
			$postTerms[$title][$postTerm['name']] = $postTerm['taxonomy'] . str_replace('|', '/', Common::builtHierarchyDown($termsOnId, $postTerm, $mergeKey) . '|' . $postTerm[$mergeKey]);
		}
		return $postTerms;
	}
	
	
	/*******************/
	/*** BreadCrumbs ***/
	/*******************/
	
	private function addBreadCrumbs(&$post, $taxonomyTitle = null, $value = null, $type = null){//dd(func_get_args());
		if($this->options['has_archive'] && !Options::front()){
			$this->config->addBreadCrumbs(\langUrl() . $this->options['has_archive'], $this->options['title']);
		}
		
		
		if($type){
			if(is_array($value)) $value = implode(' > ', $value);
			$this->addBreadCrumbsHelper($taxonomyTitle, $value, $taxonomyTitle, $post['short_title'] ?: $post['title']);
		}elseif(isset($post['id']) && $this->config->front_page != $post['id']){
			$this->config->addBreadCrumbs($post['url'], $post['short_title'] ?: $post['title']);
			//if(isset($this->options['rewrite']['slug']))
			if($this->options['title']){
				$post['h1'] = $post['title'];
				$post['title'] .= ' - ' . $this->options['title'];
			}
				
		}
			
	}
	
	private function addBreadCrumbsHelper($taxonomyTitle, $value, $text, &$postTitle){//dd(func_get_args());
		$this->config->addBreadCrumbs($taxonomyTitle, $text . ': ' . $value);
		$postTitle = $taxonomyTitle . ': ' . $value . ' | ' . $postTitle;
	}
	
	private function stats(){
		global $start;
		return $this->di->get('db')->getStats();
	}
	
	public function actionGetCaptcha(){
		(new \Jump\core\captcha\Captcha)->set();
		exit;
	}
}