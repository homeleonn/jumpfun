<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Session;
use Jump\helpers\Common;
use Jump\helpers\Msg;
use frontend\models\Login;

class UserController extends Controller{
	
	public function actionIndex(){
		// Session::set([
			// 'id' => 1,
			// 'user' => [
				// 'id' 	=> 1,
				// 'name' 	=> 'admin',
				// 'accesslevel' 	=> 1
			// ]
		// ]);
		//dd(session('user.name'));
		//$user = $this->model->get();
		$user['title'] = 'Панель пользователя ' . session('user.name');
		return $user;
	}
	
	public function actionAuth(){
		var_dump(__METHOD__);
	}
	
	public function actionLogin(){
		$data['title'] = 'Авторизация';
		return $data;
	}
	
	public function actionAddComment($postId, $product = false){
		// check exists
		$postCommentStatus = $this->db->getOne('Select comment_status from '.($product ? 'products' : 'posts').' where id = ' . $postId);
		if(!$postCommentStatus || $postCommentStatus == 'closed') Msg::jsonCode(0);
		
		$user 		= $this->model->get();
		$comment 	= htmlspecialchars($_POST['comment']);
		$ip 		= Common::ipCollect();
		$agent 		= $_SERVER['HTTP_USER_AGENT'];
		
		$this->db->query("INSERT INTO comments (comment_post_id, comment_author_id, comment_author, comment_author_email, comment_author_url, comment_author_ip, comment_author_agent, comment_content) values ({$postId}, {$user['id']}, '{$user['login']}', '{$user['email']}', '{$user['user_url']}', '{$ip}', '{$agent}', '{$comment}')");
		Msg::jsonCode(1);
	}
	
	
}