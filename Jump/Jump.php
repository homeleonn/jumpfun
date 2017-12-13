<?php

namespace Jump;

use Jump\DI\DI;

class Jump{
	private $di;
	
	public $router;
	
	public $view;
	
	public function __construct(DI $di){
		$this->di = $di;
		$this->router = $this->di->get('router');
		$this->view = $this->di->get('view');
	}
	
	public function run(){
		//echo '<h1>Jump is runned</h1>';
		
		if($this->router->searchController() && ($data = $this->router->run($this->di)) !== 0)
		{	
			//var_dump($data);
			//exit;
			$this->view->render($this->router->getController() . '/' . $this->router->getAction(), $data);
		}else{
			header('HTTP/1.1 404 Not Found');
			exit('Page not found');
		}
		//echo $this->di->get('request')->siteUrl();
		//var_dump($GLOBALS);
	}
}