<?php

namespace Jump\services;

use Jump\core\database\SafeMySQL;

class DatabaseProvider extends AbstractProvider{
	private $serviceName = 'db';
	
	public function init(){
		$db = new SafeMySQL(include ROOT . 'config.php');
		//var_dump(unserialize($db->getOne('Select option_value from wp_options where option_name = \'rewrite_rules\'')));exit;
		$this->di->set($this->serviceName, $db);
	}
}