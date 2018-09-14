<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Common;
use frontend\controllers\ReviewController as frontRC;

class ReviewController extends Controller{
	public function actionIndex(){
		(new frontRC())->actionList(true);
	}
	
	public function actionDelete($id){
		$this->db->query('Delete from reviews where id = ' . $id);
		doAction('reviewDelete');
		redirect('admin/reviews');
	}
	
	public function actionToggle($id){
		$currentStatus = $this->db->getOne('Select status from reviews where id = ' . $id);
		$this->db->query('Update reviews SET status = '.((int)$currentStatus ? 0 : 1).' where id = ' . $id);
		doAction('reviewToggle');
		redirect('admin/reviews');
	}
}