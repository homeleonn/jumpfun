<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Msg;

class UserController extends Controller{
	public function actionIndex(){
		dd(123);
	}
	
	public function actionList(){
		\Responce::notFound();
	}
	
	public function actionDelComment($commentId){
		$this->db->query('Delete from comments where comment_id = ?i', $commentId);
		Msg::jsonCode(1);
	}
}