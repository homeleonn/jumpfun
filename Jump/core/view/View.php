<?php

namespace Jump\Core\view;

use Jump\DI\DI;
use Jump\Core\view\Theme;

class View
{
    public $di;
    public $request;
	private $theme;
	private $path;
	private $template;
	
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
		
		//var_dump(get_defined_vars());exit;
		$templateFile 	= $this->theme->template($template);
		$this->template = explode('/', $template)[0] . '/';
		$this->path = $this->theme->path();
		$contentFile = $this->path . 'templates/' . $templateFile . '.php';
		//var_dump($templateFile, $template, $this->template);exit;
		if(!is_file($contentFile)){
			//var_dump('File ' . $contentFile . ' not exists!');
			$contentFile = $this->path . 'index.php';
		}
		
		include $this->path . 'header.php';
		include $contentFile;
		include $this->path . 'footer.php';
	}
	
	public function getFile($filename){
		if(!is_file($filename = $this->path . 'templates/' . $this->template . $filename . '.php')){
			var_dump('File :' . $filename . ' not exists!');
			return;
		}
		return $filename;
	}
}