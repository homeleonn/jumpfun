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
		if($this->router->searchController() && ($data = $this->router->run($this->di)) !== 0){	
			if(!$this->view->rendered())
				$this->view->render($this->router->getController() . '/' . $this->router->getAction(), $data);
		}else{
			$this->di->get('request')->location(NULL, 404);
		}
		requestStats();
	}
}