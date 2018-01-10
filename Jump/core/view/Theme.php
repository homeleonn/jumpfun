<?php

namespace Jump\Core\view;

class Theme
{
    private $config;
    public  $data;
	
	public function __construct($config){
		$this->config = $config;
	}
	
	public function isFrontPage($currentPageId){
		return $this->config->front_page == $currentPageId;
	}
	
	public function path(){
		return ROOT . (ENV == 'frontend' ? 'content/themes/default/' : 'admin/view/'); 
	}
	
	public function template($template){
		$options = $this->config->getCurrentPageOptions();
		return (ENV != 'admin' && isset($options['rewrite']['slug'])) ? preg_replace('/^\w+(\/.*)/', $options['rewrite']['slug'] .  '$1', $template) : $template;
	}
	
	public function title(){
		/*if(isset($theme->data['title']))
			return $theme->data['title'];
		
		$functionsFile = $this->path() . 'function.php';
		
		if(file_exists($functionsFile)){
			include_once $functionsFile;
		}*/
	}
	
}