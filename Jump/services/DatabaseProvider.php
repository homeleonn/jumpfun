<?php

namespace Jump\services;

use Jump\core\database\SafeMySQL;
use Jump\helpers\Common;

class DatabaseProvider extends AbstractProvider{
	private $serviceName = 'db';
	
	public function init(){
		$db = new SafeMySQL(Common::getConfig('db'));
		$this->di->set($this->serviceName, $db);
	}
}