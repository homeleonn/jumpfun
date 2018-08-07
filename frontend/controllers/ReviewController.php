<?php

namespace frontend\controllers;

use Jump\Controller;
class ReviewController extends Controller{
	
	public function actionList(){
		$reviews = $this->di->get('db')->getAll('Select * from reviews order by id DESC LIMIT 30');
		$this->view->render('reviews/index', [
			'title' => 'Отзывы наших клиентов об организации детских праздников в Одессе - FunKids', 
			'reviews' => $reviews
		]);
	}
	
	public function actionAdd(){
		if($_POST['captcha'] != $_SESSION['captcha_code']) exit('5');
		$name = htmlspecialchars($_POST['name']);
		$text = htmlspecialchars($_POST['text']);
		$this->db->query('Insert into reviews (name, text) VALUES (\''.$name.'\', \''.$text.'\')');
		echo 'Спасибо за Ваш отзыв, после проверки сообщения Ваш отзыв появится на сайте и его смогут прочитать все желающие, мы ценим каждого клиента! <br>С наилучшими пожеланиями команда FunKids.';
		exit;
	}
}