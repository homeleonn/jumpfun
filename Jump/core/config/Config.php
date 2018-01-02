<?php

namespace Jump\core\config;

use Jump\core\request\Request;
use Jump\DI\DI;
use Jump\helpers\Common;

class Config{
	
	private $db;
	
	private $di;
	
	private $options;
	
	private $breadcrumbs = [];
	public $breadcrumbsType = false;
	
	public function __construct(DI $di, $routes = NULL){
		$this->di = $di;
		$this->db = $this->di->get('db');
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
	
	public function addPageType($slug, $options){//var_dump(debug_backtrace());
		$options['slug'] = $slug;
		$options['category_slug'] = $options['type'] . '-cat';
		$options['tag_slug'] = $options['type'] . '-tag';
		$this->options['jump_pageTypes'][$options['type']] = $options;
		if(!isset($options['rewrite'])) $options['rewrite'] = true;
		if($options['rewrite'])
			$this->addRewrite($slug, $options['type']);
		
	}
	
	public function getCurrentPageOptions(){
		$options = isset($this->options['jump_pageTypes'][$this->postType]) ? $this->options['jump_pageTypes'][$this->postType] :$this->postType;
		
		if(!is_array($options))
			$option['type'] = $options;
		else
			$option = $options;
		
		return $option;
	}
	
	private function addRewrite($slug, $type){
		//var_dump(1);
		$router = $this->di->get('router');
		if(ENV != 'admin'){
			$router
				->add($slug, $type . ':list')
				// category + filters
				->add('(' . $type . '-cat)/(' . URL_PATTERN . ')', $type . ':list/$1/$2/category/$4')
				->add('(' . $type . '-tag)/(' . URL_PATTERN . ')', $type . ':list/$1/$2/tag/$4')
				->add($slug . '/style/(' . URL_PATTERN . ')', '*?post_type=' . $type. '&category_name=$matches[1]')
				->add($slug . '/(' . URL_PATTERN . ')', $type . ':single/$1');
		}else{
			$router
				->add($slug . '/terms', $type . ':termList', 'GET')
				->add($slug . '/add', $type . ':addForm', 'GET')
				->add($slug . '/add', $type . ':add', 'POST')
				->add($slug . '/add-term', $type . ':addTermForm', 'GET')
				->add($slug . '/add-term', $type . ':addTerm', 'POST')
				->add($slug . '/edit/(\d+)', $type . ':editForm/$1', 'GET')
				->add($slug . '/edit', $type . ':edit', 'POST')
				->add($slug . '/edit-term/(\d+)', $type . ':editTermForm/$1', 'GET')
				->add($slug . '/edit-term/(\d+)', $type . ':editTerm', 'POST')
				->add($slug . '/del/(post|category|tag)/(\d+)', $type . ':del/$2/$1', 'POST')
				->add($slug, $type . ':list', 'GET');
		}
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