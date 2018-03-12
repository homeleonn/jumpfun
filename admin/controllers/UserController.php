<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Msg;

class UserController extends Controller{
	public function actionList(){
		dd($this->db->getAll('Select * from users limit 20'));
	}
	
	public function actionDelComment($commentId){
		$this->db->query('Delete from comments where comment_id = ?i OR comment_parent = ?i', $commentId, $commentId);
		Msg::jsonCode(1);
	}
}