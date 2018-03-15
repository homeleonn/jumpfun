<?php

namespace Jump;

use Jump\helpers\HelperDI;

abstract class Model{
	protected $db;
	protected $request;
	
	public function __construct(){
		$this->db 		= HelperDI::get()->get('db');
		$this->request 	= HelperDI::get()->get('request');
	}
}