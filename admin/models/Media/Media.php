<?php

namespace admin\models\Media;

use Jump\Model;

class Media extends Model{
	public function getAll(){
		return $this->db->getAll('Select * from media');
	}
	
	public function insert($values){
		return $this->db->query('INSERT INTO media (src, name, mime, meta) VALUES ' . $values);
	}
}