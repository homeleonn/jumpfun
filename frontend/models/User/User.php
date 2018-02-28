<?php

namespace frontend\models\User;

use Jump\Model;

class User extends Model{
	public function get(){
		return $this->db->getRow('Select * from users where id = ' . session('id'));
	}
}
