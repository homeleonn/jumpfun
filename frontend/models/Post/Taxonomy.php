<?php

namespace frontend\models\Post;

class Taxonomy{
	private $db;
	
	public function __construct($db){
		$this->db = $db;
	}
}