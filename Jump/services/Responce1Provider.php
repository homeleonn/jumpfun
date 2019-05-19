<?php

namespace Jump\services;

use Jump\core\responce\Responce;

class Responce1Provider extends AbstractProvider{
	private $serviceName = 'responce';
	
	public function init(){
		$responce = new Responce();
		$this->di->set($this->serviceName, $responce);
	}
}