<?php

namespace Jump\services;

use Jump\core\request\Request;

class RequestProvider extends AbstractProvider{
	private $serviceName = 'request';
	
	public function init(){
		$request = new Request();
		$this->di->set($this->serviceName, $request);
	}
}