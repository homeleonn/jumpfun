<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Session;
use Jump\helpers\Common;
use Jump\helpers\Msg;
use frontend\models\Login;

class UserController extends Controller{
	
	public function actionIndex(){
		if(!isAuthorized()){
			$this->request->location(SITE_URL . 'user/login/');
		}
		$user['title'] = 'Панель пользователя ' . session('user.name');
		return $user;
	}
	
	public function actionAuth(){
		if(!isset($_POST['email']) || !isset($_POST['pass'])) exit;
		
		$user = $this->db->getRow("Select * from users where email = ?s and pass = ?s", $_POST['email'], md5(md5($_POST['pass'])));
		if($user){
			$accesslevel = $this->db->getRow("Select * from usermeta where user_id = {$user['id']} and meta_key = 'accesslevel'");
			Session::set([
				'id' => $user['id'],
				'user' => [
					'id' 	=> $user['id'],
					'name' 	=> $user['login'],
					'accesslevel' => isset($accesslevel) ? $accesslevel : 0
				]
			]);
			
			$this->request->location(SITE_URL . 'user/');
		}
		
		sleep(2);
		$this->request->location(SITE_URL . 'user/login/');
	}
	
	public function actionLogin(){
		// Session::push('user', ['accesslevel' => 1]);
		if(isAuthorized()){
			$this->request->location(SITE_URL . 'user/');
		}
		$data['title'] = 'Авторизация';
		return $data;
	}
	
	public function actionExit(){
		if(isAuthorized()){
			session_destroy();
			$this->request->location(SITE_URL);
		}
	}
	
	public function actionAddComment($postId, $product = false){
		// check exists
		if(!isAuthorized()) exit;
		
		$postCommentStatus = $this->db->getOne('Select comment_status from '.($product ? 'products' : 'posts').' where id = ' . $postId);
		if($postCommentStatus != 'open') Msg::jsonCode(0);
		
		$user 		= $this->model->get(session('id'));
		$comment 	= htmlspecialchars($_POST['comment']);
		$ip 		= Common::ipCollect();
		$agent 		= $_SERVER['HTTP_USER_AGENT'];
		$date 		= date('Y-m-d H:i:s');
		
		//$this->db->query("INSERT INTO comments (comment_post_id, comment_author_id, comment_author, comment_author_email, comment_author_url, comment_author_ip, comment_author_agent, comment_content) values ({$postId}, {$user['id']}, '{$user['login']}', '{$user['email']}', '{$user['user_url']}', '{$ip}', '{$agent}', '{$comment}')");
		Msg::json(themeHTMLCommentTable([
			'comment_author' 	=> $user['login'],
			'comment_date' 		=> $date,
			'comment_content' 	=> $comment,
		], ((int)$_POST['comment_count']) + 1));
	}
}