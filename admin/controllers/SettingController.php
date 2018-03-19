<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Common;

class SettingController extends Controller{
	public function actionIndex(){
		global $di;
		$data['settings']['front_page'] = Common::getOption('front_page');
		$data['settings']['title'] = Common::getOption('title');
		$data['settings']['description'] = Common::getOption('description');
		$post = new \admin\models\Post\Post(new \frontend\models\Post\Taxonomy($di));
		$parent = is_numeric($data['settings']['front_page']) ? $data['settings']['front_page'] : NULL;
		$data['settings']['listForParents'] = $post->listForParents(NULL, $parent);
		return $data;
	}
	
	public function actionSave(){
		//dd($_POST);
		$this->saveMainPageData();
		redirect('admin/settings');
	}
	
	private function saveMainPageData(){
		if(isset($_POST['front_page'])){
			$save = false;
			switch($_POST['front_page']){
				case 'last':
					$save = 'last';
				break;
				case 'static':
					if(isset($_POST['parent']) && is_numeric($_POST['parent']) && $_POST['parent']){
						$save = $_POST['parent'];
					}
				break;
			}
			if($save){
				Common::setOption('front_page', $save); 
			}
		}
	}
}