<?php

namespace frontend\controllers;

\session_start();

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
		if(!isset($_POST['email']) || !isset($_POST['pass']) || !isset($_POST['token']) || !token($_POST['token'])){
			$this->authFail();
		}
		
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
		
		$this->authFail();
	}
	
	public function authFail(){
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
	
	public function actionAddComment($postId, $product = false){//dd($_POST);
		// check exists
		//if(!isAuthorized()) exit;
		
		if(!isAdmin() && !(new \Jump\core\captcha\Captcha)->validation()){
			Msg::set(['error' => 'Неверно введены защитные символы']);
		}
		
		$postCommentStatus = $this->db->getOne('Select comment_status from '.($product ? 'products' : 'posts').' where id = ' . $postId);
		if($postCommentStatus != 'open') Msg::jsonCode(0);
		
		$login = htmlspecialchars($_POST['login']);
		if(isAuthorized())
			$user = $this->model->get(session('id'));
		else
			$user = [
				'id' 		=> '',
				'login' 	=> $login,
				'email' 	=> '',
				'user_url' 	=> '',
			];
		$comment 	= htmlspecialchars($_POST['comment']);
		$ip 		= Common::ipCollect();
		$agent 		= $_SERVER['HTTP_USER_AGENT'];
		$date 		= date('Y-m-d H:i:s');
		$comment_parent = (int)$_POST['comment_parent'];
		
		$this->db->query("INSERT INTO comments (comment_post_id, comment_author_id, comment_author, comment_author_email, comment_author_url, comment_author_ip, comment_author_agent, comment_content, comment_parent) values ('{$postId}', '{$user['id']}', '{$user['login']}', '{$user['email']}', '{$user['user_url']}', '{$ip}', '{$agent}', ?s, '{$comment_parent}')", $comment);
		Msg::set(['comment' => themeHTMLCommentTable([
			'comment_id' 		=> $this->db->insertId(),
			'comment_author' 	=> $user['login'],
			'comment_date' 		=> $date,
			'comment_content' 	=> $comment,
			'comment_parent' 	=> $comment_parent,
			'comment_count' 	=> (int)$_POST['comment_count'] + 1,
		]), 'comment_parent' => $comment_parent]);
	}
}