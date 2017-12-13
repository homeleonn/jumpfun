<?php

namespace Jump\services;

use Jump\core\view\View;
use Jump\core\view\Theme;

class ViewProvider extends AbstractProvider{
	private $serviceName = 'view';
	
	public function init(){
		$theme = new Theme($this->di->get('config'));
		$view  = new View($this->di, $theme);
		
		$this->di->set($this->serviceName, $view);
	}
}