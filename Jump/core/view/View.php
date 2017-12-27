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

    public function render($template, $data = [])
    {//var_dump($data);exit;
		if(ENV != 'admin'){
			$this->theme->data = $data;
		
			if(is_array($data)){
				extract($data);
				unset($data);
			}
		}else{
			$options = $this->di->get('config')->getCurrentPageOptions();
		}
		
		$templateFile 	= $this->theme->template($template);
		$this->path = $this->getPath($template);
		$contentFile = $this->path . 'templates/' . $templateFile . '.php';
		if(!is_file($contentFile)){
			//var_dump('File ' . $contentFile . ' not exists!');
			$contentFile = $this->path . 'index.php';
		}
		
		include $this->path . 'header.php';
		$this->cacheStart();
		include $contentFile;
		$this->cacheStop();
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
}