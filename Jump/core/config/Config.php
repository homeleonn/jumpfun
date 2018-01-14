<?php

namespace Jump\core\config;

use Jump\helpers\Common;
use Jump\helpers\HelperDI;

class Config{
	const POSTS_PER_PAGE = 20;
	private $db;
	private $router;
	private $options;
	public $pageTypesWithFront = [];
	
	private $breadcrumbs = [];
	public $breadcrumbsType = false;
	
	public function __construct($db, $routes = NULL){
		$this->db 		= $db;
		$this->options['postType'] = false;
		
		// Загружаем обязательные настройки сайта из базы данных
		$this->optionsLoad(true);
	}
	
	public function optionsLoad($fromFile = false){
		if($fromFile){
			$this->options = array_merge($this->options, Common::getConfig('options'));
			return;
		}
		$options = $this->db->getAll('Select name, option_value from options where autoload = \'yes\'');
		
		if($options){
			foreach($options as $option){
				$this->options[$option['name']] = $option['option_value'];
			}
		}
	}
	
	public function getOption($name){
		if(isset($this->options[$name]))
			return $this->options[$name];
		$this->options[$name] = $this->db->getOne('Select option_value from options where name = ?s', $name);
		return $this->options[$name];
	}
	
	public function setOption($name, $value){
		$this->options[$name] = $value;
	}
	
	public function addPageType($options){
		if(!isset($options['icon'])) $options['icon'] = 'tags';
		if(!isset($options['has_archive']) || $options['has_archive'] === true) 			
			$options['has_archive'] = $options['type'];
		if(!isset($options['rewrite'])) $options['rewrite'] = false;
		if($options['rewrite']){
			if(!isset($options['rewrite']['with_front'])) 	$options['rewrite']['with_front'] = false;
			if(!isset($options['rewrite']['slug']))			$options['rewrite']['slug']	= $options['type'];
			if(!isset($options['rewrite']['paged'])) 		$options['rewrite']['paged'] = self::POSTS_PER_PAGE;
			if(!isset($options['taxonomy'])) 				$options['taxonomy'] = [];
			
			// $replaceCount = 0;
			// $slug = preg_replace('~%.+%~', '([^/]+)', $options['rewrite']['slug'], -1, $replaceCount);
			// $addArgs = '';
			// $i = 2;
			// while($replaceCount--){
				// $addArgs .= '/$' . $i++;
			// }
			$type = $options['type'];
			$sep  = '/';
			
			
			// Routing
			$router = HelperDI::get('router');
			if(ENV != 'admin'){
				$paged = $options['rewrite']['paged'] ? "({$sep}page/([2-9]|\d{2,}))?" : '';
				//$router->add($singleSlug . $sep . '(' . URL_PATTERN . ')', $type . ':single/$1' . $addArgs);
				if($options['has_archive']){
					$router->add($options['has_archive'] . $paged, $type . ':list'.S.S.S.'$1');
				}
				if(!empty($options['taxonomy'])){
					if($options['has_archive'] === false) $sep = '';
					foreach($options['taxonomy'] as $t => $values)
						$router->add("{$options['has_archive']}{$sep}{$t}/(.*)" . $paged, $type . ":list|{$t}|$1|$3");
				}
			}else{
				$router
					->add($type . '/terms', $type . ':termList', 'GET')
					->add($type . '/add', $type . ':addForm', 'GET')
					->add($type . '/add', $type . ':add', 'POST')
					->add($type . '/add-term', $type . ':addTermForm', 'GET')
					->add($type . '/add-term', $type . ':addTerm', 'POST')
					->add($type . '/edit/(\d+)', $type . ':editForm/$1', 'GET')
					->add($type . '/edit', $type . ':edit', 'POST')
					->add($type . '/edit-term/(\d+)', $type . ':editTermForm/$1', 'GET')
					->add($type . '/edit-term/(\d+)', $type . ':editTerm', 'POST')
					->add($type . '/del/(post|term)/(\d+)', $type . ':del/$2/$1', 'POST')
					->add($type, $type . ':list', 'GET');
			}
		}
		
		$this->options['jump_pageTypes'][$options['type']] = $options;
	}
	
	public function getCurrentPageOptions(){
		$options = isset($this->options['jump_pageTypes'][$this->postType]) ? $this->options['jump_pageTypes'][$this->postType] :$this->postType;
		
		if(!is_array($options))
			$option['type'] = $options;
		else
			$option = $options;
		
		return $option;
	}
	
	public function getPageOptionsByType($type){
		$options = isset($this->options['jump_pageTypes'][$type]) ? $this->options['jump_pageTypes'][$type] : NULL;
		return $options;
	}
		
	public function __get($option){//var_dump($option, $this->getOption($option));
		return $this->getOption($option);
	}
	
	public function addBreadCrumbs($link, $name){
		//var_dump($link, $name);exit;
		$this->breadcrumbs[($link . '/')] = $name;
	}
	
	public function getBreadCrumbs(){
		if(is_string($this->breadcrumbs)) return $this->breadcrumbs;
		$s = '';
		$snake = '';
		$breadcrumbsLength = count($this->breadcrumbs);
		//echo $breadcrumbsLength;
		//if($breadcrumbsLength == 1) return;
		if($this->breadcrumbsType){
			$this->breadcrumbs = array_reverse($this->breadcrumbs);
		}
		if($breadcrumbsLength)
			$this->breadcrumbs = array_merge(['' => 'Главная'], $this->breadcrumbs);
		$endElement = array_pop($this->breadcrumbs);
		
			
		$i = 0;
		foreach($this->breadcrumbs as $link => $name){
			if(!$this->breadcrumbsType){
				$snake .= (!$i ? SITE_URL : '') . $link;
				$s .= '<a href="'.$snake.'">'.$name.'</a> > ';
			}else{
				$s .= '<a href="'.SITE_URL.$link.'">'.$name.'</a> > ';
			}
			$i++;
		}
		
		//$breadcrumbs = $breadcrumbsLength > 1 ? $s . $endElement : substr($s, 0, -3);
		$breadcrumbs = $s . $endElement;
		$this->breadcrumbs = '<div id="breadcrumbs">' . $breadcrumbs . '</div>';
		return $this->breadcrumbs;
	}
}