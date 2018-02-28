<?php

namespace Jump\services;

use Jump\core\responce\Responce;

class ResponceProvider extends AbstractProvider{
	private $serviceName = 'responce';
	
	public function init(){
		$responce = new Responce();
		$this->di->set($this->serviceName, $responce);
	}
}