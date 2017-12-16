<?php

namespace Jump\core\config;

use Jump\core\request\Request;
use Jump\DI\DI;

class Config{
	
	private $db;
	
	private $di;
	
	private $options;
	
	private $breadcrumbs = [SITE_URL => 'Главная'];
	
	public function __construct(DI $di, $routes = NULL){
		$this->di = $di;
		$this->db = $this->di->get('db');
		
		// Загружаем обязательные настройки сайта из базы данных
		$this->optionsLoad();
	}
	
	public function optionsLoad(){
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
		
		$optionValue = $this->db->getOne('Select option_value from options where name = ?s', $name);
		
		if($optionValue){
			$this->options[$name] = $optionValue;
			return $this->options[$name];
		}
		
		return NULL;
	}
	
	public function setOption($name, $value){
		$this->options[$name] = $value;
	}
	
	public function addPageType($slug, $options){//var_dump(debug_backtrace());
		$options['slug'] = $slug;
		$options['category_slug'] = $options['type'] . '-cat';
		$options['tag_slug'] = $options['type'] . '-tag';
		$this->options['jump_pageTypes'][$options['type']] = $options;
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
				$filtersRegExp = '[a-zA-Z0-9-,=;]+[^;]';
				
				$router
					->add($slug, $type . ':list')
					->add($slug . '/(' . $filtersRegExp . ')', $type . ':list/////$1')
					// category + filters
					->add('(' . $type . '-cat)/(' . URL_PATTERN . ')' . '(/(' . $filtersRegExp . '))?', $type . ':list/$1/$2///$4')
					->add('(' . $type . '-tag)/(' . URL_PATTERN . ')' . '(/(' . $filtersRegExp . '))?', $type . ':list///$1/$2/$4')
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
		$s = '';
		$snake = '';
		//var_dump($this->breadcrumbs);
		$breadcrumbsLength = count($this->breadcrumbs);
		
		if($breadcrumbsLength == 1) return;
		
		$endElement = array_pop($this->breadcrumbs);
		//var_dump($this->breadcrumbs);
		foreach($this->breadcrumbs as $link => $name){
			$snake .= $link;
			$s .= '<a href="'.$snake.'">'.$name.'</a> > ';
		}
		
		return $breadcrumbsLength > 1 ? $s . $endElement : substr($s, 0, -3);
	}
}