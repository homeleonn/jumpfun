<?php

namespace Jump;

use Jump\helpers\HelperDI;

abstract class Model{
	protected $db;
	protected $request;
	
	private $className;
	protected $table;
	
	public function __construct(){
		$this->db 		 = HelperDI::get()->get('db');
		$this->request 	 = HelperDI::get()->get('request');
		$this->className = $this->className(get_called_class());
		$this->table 	 = $this->table ?: $this->tableName();
	}
	
	public function all(){
		return $this->db->getAll('Select * from ' . $this->table);
	}
	
	private function className($namespace){
		$parts = explode('\\', $namespace);
		return end($parts);
	}
	
	private function tableName(){
		return mb_strtolower($this->className) . 's';
	}
}