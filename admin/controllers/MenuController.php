<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Msg;
use Jump\helpers\Common;

class MenuController extends Controller{
	public function actionIndex(){
		// creating new menu
		if(isset($_POST['new_menu'])){
			$error = '';
			$newMenu = trim($_POST['new_menu']);
			if(!$newMenu){
				$error .= 'Введите название меню';
				setCookie('error', $error);
				$this->request->refresh();
			}
			
			if(!$this->add($newMenu)){
				setCookie('error', 'Меню с таким названием уже существует');
			}else{
				setCookie('menu', 'Меню успешно создано');
			}
			
			$this->request->refresh();
		}elseif(isset($_POST['del_menu_id'])){
			$this->del();
		}
			
		//$data['categories'] = Catalog::getAllCategories();
		//$data['pages'] 		= Page::getAllPages();
		$data['title'] = 'Меню';
		
		$posts 		= $this->db->getAll('Select * from posts order by created ASC');
		$postsOnTypes = Common::itemsOnKeys($posts, ['post_type']);
		//$data['types'] = array_keys($postsOnTypes);
		
		global $di;
		$taxonomy = new \frontend\models\Post\Taxonomy($di->get('db'));
		$post = new \admin\models\Post\Post($taxonomy);
		$postFront = new \frontend\models\Post\Post($taxonomy);
		$postControllerFront = new \frontend\controllers\PostController;
		//$data['pages'] 		= $post->listForParents($postsOnTypes['page'], NULL, TRUE);
		//unset($postsOnTypes['page']);
		
		if(!empty($postsOnTypes))
		{
			$allTaxonomies = $allPostIds = $taxonomies = [];
			foreach($postsOnTypes as $postType => &$posts)
			{
				if(!$options = $di->get('config')->getPageOptionsByType($postType))
					continue;
				
				$data['types'][$options['type']] = $options;
				//d($options['taxonomy']);
				if(isset($options['taxonomy'])){
					foreach($options['taxonomy'] as &$tax) $tax['archive'] = $options['has_archive'];
					//d($options['taxonomy']);
					$taxonomies = array_merge($taxonomies, $options['taxonomy']);
				}
				
				if($options['hierarchical'])
					$posts = $post->listForParents($posts, NULL, TRUE);
				else{
					$allPostIds 	= array_merge($allPostIds, Common::getKeys($posts, 'id'));
					$allTaxonomies 	= array_merge($allTaxonomies, array_keys($options['taxonomy']));
				}
			}
			//dd($taxonomies);
			
			$postsTerms = $post->taxonomy->getByTaxonomies($allTaxonomies);
			
			$relatedPostTerms = $postFront->getPostsTerms(' and tr.object_id IN( ' . implode(',', $allPostIds) . ') and tt.taxonomy IN(\''.implode("','", $allTaxonomies).'\')');
			$relatedPostTerms = Common::itemsOnKeys($relatedPostTerms, ['object_id']);
			
			list($termsOnId, $termsOnParent, $termsOnTaxonomies) = Common::itemsOnKeys($postsTerms, ['id', 'parent', 'taxonomy']);
			
			//dd($postsTerms, $allTaxonomies, $allPostIds, $relatedPostTerms, $termsOnId, $termsOnParent);
			//dd($post->hierarchyItems($postsTerms));
			
			foreach($postsOnTypes as $postType => &$posts)
			{
				$options = $data['types'][$postType] ?? null;
				//d($options);
				if($options && !$options['hierarchical'])
				{
					foreach($posts as &$onePost)
					{
						$postControllerFront->postPermalink($onePost, $termsOnId, $termsOnParent, $relatedPostTerms[$onePost['id']] ?? null, $options['rewrite']['slug']);
					}
				}
			}
			
			if(!empty($taxonomies)){
				$data['types']['taxonomies'] = $taxonomies;
				foreach($taxonomies as $tax1 => $opt){
					$postsOnTypes['taxonomies'][$tax1] = $post->hierarchyItems($termsOnTaxonomies[$tax1]);
				}
			}
			
			
		}
		
		$data['posts_on_types'] = $postsOnTypes;
		
		$data['menus'] 		= $this->menus();
		$data['menuItems'] 	= $this->menuItems($data['menus']['id']);
		return $data;
	}
	
	public function actionEdit(){
		$menu = json_decode($_POST['menu']);
		if(empty($menu)) return;
		//dd($menu, $_POST['menu']);
		$queryFull = 'INSERT INTO menu (menu_id, object_id, name, origname, url, type, parent, `sort`) VALUES ';
		$queryItems = ''; 
		$sort = $subSort = 0;
		foreach($menu as $item){
			$queryItems .= $this->cols($item, -1, $sort++, $_POST['menu_id']);
			
			if(isset($item->children)){
				$subSort = 0;
				foreach($item->children as $childItem){
					$queryItems .= $this->cols($childItem, $item->id, $subSort++, $_POST['menu_id']);
				}
			}
		}
		$queryFull .= substr($queryItems, 0, -1);
		$this->db->query('Delete from menu where menu_id = ' . $_POST['menu_id']);
		$this->db->query($queryFull);
		unlink(UPLOADS_DIR . 'cache/menu/menu.html');
	}
	
	public function del(){
		$id = (int)$_POST['del_menu_id'];
		//$menu = $this->db->getAll('Select * from options where name = "menu"', 'row');
		//$menu = $this->db->getAll('Select * from options where name = "menu"', 'row');
		//Common::getOption('menu', 'a:2:{i:0;a:2:{s:2:"id";i:1;s:4:"name";s:5:"first";}i:1;a:2:{s:2:"id";i:2;s:4:"name";s:1:"1";}}');
		
		//$menus = unserialize($menu['value']);
		$menus = unserialize(Common::getOption('menu'));
		
		//var_dump($menu, $menus);exit;
		
		if(count($menus) == 1){
			setCookie('error', 'Последнее меню удалять нельзя, отредактируйте его или создайте новое.');
		}else{
			$newMenu = array();
			foreach($menus as $k => $menu){
				if($menu['id'] == $id){
					$issetThisMenu = true;
					continue;
				}
				$newMenu[] = $menu;
			}
			if(!isset($issetThisMenu)) exit('Меню не найдено!');
			
			$activeMenu = $newMenu[0]['id'];
			$menu['value'] = serialize($newMenu);
			$this->db->query("Update options SET value = 
				CASE
					WHEN name = 'menu' THEN '".serialize($newMenu)."'
					WHEN name = 'menu_active_id' THEN '{$activeMenu}'
				END 
				WHERE name = 'menu' OR name = 'menu_active_id' LIMIT 2");
				
			$this->db->query('Delete from menu where menu_id = ' . $id);
		}
		setCookie('menu', 'Выбранное меню успешно удалено!');
		redirect('self');
	}
	
	public function actionActivate(){
		//exit($this->db->query('UPDATE options SET value = "' . ((int)$_POST['id']) . '" where name = "menu_active_id"'));
		Common::setOption('menu_active_id', (int)$_POST['id']);
	}
	
	public function actionSelect(){
		if(!isset($_POST['id']) || !is_numeric($_POST['id'])) exit;
		exit(json_encode($this->db->getAll('Select * from menu where menu_id = ' . $_POST['id'])));
	}
	
	
	private function menus(){
		//$menu = $this->db->getAll('Select name, value from options where name = "menu" OR name = "menu_active_id" LIMIT 2');
		// $menu = [
			// ['name' => 'menu', 'value' => 'a:2:{i:0;a:2:{s:2:"id";i:1;s:4:"name";s:5:"first";}i:1;a:2:{s:2:"id";i:2;s:4:"name";s:1:"1";}}'],
			// ['name' => 'menu_active_id', 'value' => '1'],
		// ];
		
		// if(!$menu) return false;
		
		// if($menu[0]['name'] == 'menu'){
			// $menu['list'] = unserialize($menu[0]['value']);
			// $menu['id']   = $menu[1]['value'];
		// }else{
			// $menu['list'] = unserialize($menu[1]['value']);
			// $menu['id']   = $menu[0]['value'];
		// }
		
		$menu['list'] = unserialize(Common::getOption('menu'));
		$menu['id']   = Common::getOption('menu_active_id');
		
		//dd($menu);
		return $menu;
	}
	
	private function add($name){
		$menus = $this->menus() ?: array();
		$menus = $menus['list'];
		$isset = false;
		$maxId = 0;
		
		if($menus){
			$maxId = 1;
			foreach($menus as $menu){
				if($menu['name'] == $name){
					$isset = true;
					break;
				}
				if($menu['id'] > $maxId) $maxId = $menu['id'];
			}
		}
		
		if(!$isset){
			$menu = array('id' => ++$maxId, 'name' => $name);
			$empty = empty($menus);
			$menus[] = $menu;
			$newMenu = serialize($menus);
			Common::setOption('menu', $newMenu);
			// if($empty)
				// $this->db->query('INSERT INTO options (name, value, autoload) VALUES (\'menu\', \''.$newMenu.'\', "no")');
			// else
				// $this->db->query('Update options set value = \''.$newMenu.'\' where name = "menu" LIMIT 1');
		}
		
		return !$isset;
	}
	
	private function cols($item, $parent, $sort, $menuId){
		return "(
			{$menuId},
			{$item->id},
			'{$item->name}',
			'{$item->origname}',
			'{$item->url}',
			'{$item->type}',
			{$parent},
			{$sort}
		),";
	}
	
	private function menuItems($menuId){
		return $this->db->getAll('Select * from menu where menu_id = ' . $menuId);
	}
}