<?php

namespace Jump\services;

use Jump\core\config\Config;

class ConfigProvider extends AbstractProvider{
	private $serviceName = 'config';
	
	public function init(){
		$config  = new Config($this->di);
		
		$this->di->set($this->serviceName, $config);
	}
}