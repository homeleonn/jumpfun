<?php

namespace frontend\controllers;

class SearchController{
	public function actionIndex(){
		$data['title'] 	= 'Поиск по сайту';
		$data['search'] = '';
		if(!isset($_GET['q'])){
			return $data;
		}
		
		$data['search'] = $search = htmlspecialchars($_GET['q']);
		$data['title'] .= ' | ' . $search;
		return $data;
	}
}