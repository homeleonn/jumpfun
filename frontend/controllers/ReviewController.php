<?php

namespace frontend\controllers;

use Jump\Controller;
use Jump\helpers\Common;

class ReviewController extends Controller{
	
	public function actionList($all = false){
		$reviews = $this->di->get('db')->getAll('Select * from reviews where 1=1 '.(!$all ? 'and status = 1 ':'').'order by id DESC LIMIT 30');
		$this->config->addBreadCrumbs('reviews', 'Отзывы');
		$this->view->render('reviews/index', [
			'title' => 'Отзывы наших клиентов об организации детских торжеств в Одессе - FunKids', 
			'reviews' => $reviews
		]);
	}
	
	public function add(){
		$name = $this->clearMsg($_POST['name'], 50);
		$text = $this->clearMsg($_POST['text'], 500);
		$this->db->query('Insert into reviews (name, text) VALUES (?s, ?s)', $name, $text);
		exit('Спасибо за Ваш отзыв, после проверки сообщения Ваш отзыв появится на сайте и его смогут прочитать все желающие, мы ценим каждого клиента! <br>С наилучшими пожеланиями. © команда FunKids');
	}
	
	public function actionMail(){//d($_REQUEST, session());
	
		if(!isset($_POST['type'])){
			exit('999');
		}
		
		$goMail = false;
		
		$currentIp = Common::ipCollect();
		$timeoutIpStorage = UPLOADS_DIR . 'mailTimeout.txt';
		if(is_file($timeoutIpStorage)){
			$times = unserialize(file_get_contents($timeoutIpStorage));
		}else{
			$times = [];
		}	
		
			
		// timeout
		if(isset($times[$currentIp]) && $times[$currentIp] + 60 > time())
		{
			if(!isset($_POST['captcha']))
			{
				exit('1');
			}
			else
			{
				if(session('captcha_code') === $_POST['captcha'])
				{
					$goMail = true;
				}else{
					exit($_POST['captcha']?'0':'1');
				}
			}
		}
		else
		{
			$goMail = true;
		}
			
		if($goMail){
			$times[$currentIp] = time();
			file_put_contents($timeoutIpStorage, serialize($times), LOCK_EX);
			
			$mess = '
			<div style="width: 90%; margin: 0 auto;font-family: tahoma, times, sans-ms;">
				<h1 style="padding: 10px;background: royalblue; color: white; font-weight: bold; margin-bottom: 20px;border-radius: 10px;text-align: center;">Заявка с сайта FunKids</h1>
				<table width="100%" cellspacing="0" cellpadding="5" border="1">';
				
			switch($_POST['type']){
				case '3':{
					$tel = $this->clearMsg($_POST['tel'], 50);
					$mess .= '
						<tr><td colspan="2">Пользователь запросил обратный звонок</td></tr>
						<tr><td width="20%">Телефон</td><td>'.$tel.'</td></tr>
						<tr><td>Айпи</td><td>'.$currentIp.'</td></tr>
					';
				}break;
				case '2':{
					$name = $this->clearMsg($_POST['name'], 50);
					$mail = $this->clearMsg($_POST['mail'], 100);
					$tel = $this->clearMsg($_POST['tel'], 50);
					$text = $this->clearMsg($_POST['text'], 500);
					
					$mess .= '
						<tr><td width="20%">Имя:</td><td>'.$name.'</td></tr>
						<tr><td>Контактный телефон</td><td>'.$tel.'</td></tr>
						<tr><td>Контактная почта</td><td>'.$mail.'</td></tr>
						<tr><td>Сообщение</td><td>'.$text.'</td></tr>
						<tr><td>Айпи</td><td>'.$currentIp.'</td></tr>
					';
				}break;
				case '4':{
					$this->add();
					return;
				}break;
				
				default: return;
			}
			
			
			$mess .= '</table></div>';
			
			$mail_title = 'Заявка с сайта FunKids';
			$to = '<funkidssodessa@gmail.com>, <wirus@ukr.net>';
			$from = 'FunKids';
			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
			$headers .= "From:info@funkids.od.ua\r\n";
			//mail($to, $mail_title, $mess, $headers);
			
			exit('Спасибо за Ваше сообщение. <br>С наилучшими пожеланиями. © команда FunKids');
		}
		exit;
	}
	
	private function clearMsg($text, $cut){
		return htmlspecialchars(trim(mb_substr($text, 0, $cut)), ENT_NOQUOTES);
	}
}