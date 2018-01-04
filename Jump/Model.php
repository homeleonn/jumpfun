<?php

namespace Jump;

use Jump\DI\DI;

abstract class Model{
	protected $db;
	protected $request;
	
	public function __construct(DI $di){
		$this->db 		= $di->get('db');
		$this->request 	= $di->get('request');
	}
}