<?php

namespace Jump\Core\view;

use Jump\DI\DI;
use Jump\Core\view\Theme;
use Jump\helpers\Common;

class View
{
    public $di;
    public $request;
	private $theme;
	private $children = NULL;
	private $senderModel;
	private $path;
	private $template;
	public $rendered = false;
	public $cache = false;
	public $cacheFileName = false;
	
    public function __construct(DI $di, Theme $theme)
    {
        $this->di = $di;
        $this->request = $this->di->get('request');
        $this->theme = $theme;
    }

    public function render($template, $data = [], $withBlocks = true){//var_dump(get_defined_vars());exit;
		$contentFile = '';
		$this->path = $this->getPath($template);
		if(ENV != 'admin'){
			$this->theme->data = $data;
		
			if(is_array($data)){
				extract($data);
				unset($data);
			}//var_dump(get_defined_vars());exit;
			if(isset($__list)){
				$this->children = $__list;
				unset($__list);
			}
			if(isset($__model)){
				$this->senderModel = $__model;
				unset($__model);
			}
			if(isset($_jmp_post_template) && $_jmp_post_template){
				$contentFile = $_jmp_post_template;
			}elseif(isset($title))
				$contentFile = 'page-' . $title . '.php';	
			$contentFile = $this->path . $contentFile;	
		}else{
			$options = $this->di->get('config')->getCurrentPageOptions();
			if(!file_exists($contentFile)){
				$templateFile 	= $this->theme->template($template);
				$contentFile = $this->path . 'templates/' . $templateFile . '.php';
			}
		}
		
		// if(!file_exists($contentFile)){
			// $templateFile 	= $this->theme->template($template);
			// $contentFile = $this->path . 'templates/' . $templateFile . '.php';
		// }
		
		if(!file_exists($contentFile)){
			$contentFile = $this->path . $this->is() . '.php';
			//var_dump($contentFile);
		}
		
		if(!file_exists($contentFile)){
			//var_dump('File ' . $contentFile . ' not exists!');
			$contentFile = $this->path . 'index.php';
		}
		if($withBlocks)
			include $this->path . 'header.php';
		$this->cacheStart();
		include $contentFile;
		$this->cacheStop();
		if($withBlocks)
			include $this->path . 'footer.php';
		$this->rendered = true;
	}
	
	public function rendered(){
		return $this->rendered;
	}
	
	public function getPath($template){
		$this->template = explode('/', $template)[0] . '/';
		return $this->theme->path();
	}
	
	public function getFile($filename){
 		if(!is_file($filename = $this->path . 'templates/' . $this->template . $filename . '.php')){
 			var_dump('File :' . $filename . ' not exists!');
 			return;
 		}
 		return $filename;
 	}
	
	
	private function cacheStart(){
		if($this->cache){ 
			ob_start();
		}
	}
	
	private function cacheStop(){
		if($this->cache){
			$this->setMetaForCache();
			$data = ob_get_clean();
			file_put_contents($this->cacheFileName, (string)($this->di->get('config')->getBreadCrumbs() . $data), LOCK_EX);
			echo $data;
			$this->rendered = true;
		}
	}
	
	public function cacheOn($cacheFileName){
		$this->cache = true;
		$this->cacheFileName = $cacheFileName;
	}
	
	private function setMetaForCache(){
		if($this->cache)
			echo "<script>var postData = {\"title\":\"{$this->theme->data['title']}\"}</script>";
	}
	
	private function haveChild($id = 0){
		if(is_null($this->children)){
			if(!isset($this->senderModel)) return;
			$this->children = $this->senderModel->getChildrens($id);
			if(!$this->children) $this->children = false;
		}
		return $this->children;
	}
	
	private function theChild(){
		if(!$this->children || !($child = current($this->children))) return false;
		next($this->children);
		return $child;
	}
	
	public function __get($property){
		if($property == 'date')
			return \Jump\helpers\MyDate::class;
	}
	
	/**
	 *  set or get template
	 *  
	 *  @param type $template
	 *  
	 */
	public function is($template = NULL){
		$baseTemplate = 'index';
		if($template){
			$validTemplates = ['list', 'single'];
			if(!in_array($template, $validTemplates))
				$template = $baseTemplate;
			$this->templateFile = $template;
		}elseif(!$this->templateFile){
			return $baseTemplate;
		}else{
			return $this->templateFile;
		}
	}
	
	
	private function getSingleLink($url, $slug){
		//return str_replace()
	}
}