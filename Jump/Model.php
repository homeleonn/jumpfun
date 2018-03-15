<?php

namespace Jump;

use Jump\helpers\HelperDI;

abstract class Model{
	protected $db;
	protected $request;
	private $model;
	private $table;
	
	public function __construct(){
		$this->db 				= HelperDI::get()->get('db');
		$this->request 			= HelperDI::get()->get('request');
		$modelNamespace 		= explode('\\', get_called_class());
		$this->model 			= $modelNamespace[count($modelNamespace) - 1];
		if(!isset($this->table))
			$this->table 			= mb_strtolower($this->model) . 's';
		//$this->relationship 	= 
		//dd($this->modelTableName);
	}
	
	public function belongsToMany($modelNamespace, $fields = []){
		
		dd((new $modelNamespace)->all())
		return $this->db->getAll("Select t.* from term_relationships tr INNER JOIN term_taxonomy tt ON(tt.term_taxonomy_id = tr.term_taxonomy_id) INNER JOIN terms t ON(t.id = tt.term_id) where tr.object_id = " . $this->id);
	}
	
	
	public function find(int $id){
		return $this->set($this->db->getRow("Select * from `{$this->table}` where `id` = '{$id}'"));
	}
	
	public function set(array $foo){
		foreach($foo as $k => $f){
			$this->$k = $f;
		}
		return $this;
	}
	
	public function all(){
		
	}
	
	public function __get($property){
		return $this->$property();
	}
}