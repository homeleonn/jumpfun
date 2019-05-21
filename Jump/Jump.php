<?php

namespace Jump;

use Jump\DI\DI;
use Jump\core\responce\Responce;

class Jump
{
	private $di;
	private $router;
	private $view;
	
	public function __construct(DI $di)
	{
		$this->di 		= $di;
		$this->router 	= $this->di->get('router');
		$this->view 	= $this->di->get('view');
	}
	
	public function run()
	{
		if ($this->router->searchController() && ($data = $this->router->run($this->di)) !== 0) {
			if (!$this->view->rendered()) {
				$this->view->render($this->router->getController() . '/' . $this->router->getAction(), $data);
			}
		} else {
			(new Responce())->view('404', Responce::HTTP_NOT_FOUND);
		}
		requestStats();
		echo '<!--', scriptTime(), '-->';
	}
}