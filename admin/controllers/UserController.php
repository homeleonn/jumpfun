<?php

namespace admin\controllers;

use Jump\Controller;
use Jump\helpers\Msg;
use Jump\helpers\Common;

class UserController extends Controller{
	public function actionList(){
		$users = $this->db->getAll('Select * from users limit 20');
		foreach($users as $user){
			$data['users'][$user['id']] = $user;
			$usersIds[] = $user['id'];
		}
		$userMeta = $this->db->getAll('Select * from usermeta where user_id IN('.implode(',', $usersIds).')');
		
		foreach($userMeta as $meta){
			$data['users'][$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
		}
			
		//dd($data['users']);
		return $data;
	}
	
	public function actionDelComment($commentId){//dd(func_get_args());
		$ids = $this->db->getAll('Select comment_id from comments where comment_parent = ?i', $commentId);
		$this->db->query('Delete from comments where comment_id = ?i OR comment_parent = ?i', $commentId, $commentId);
		Msg::set(['children' => $ids, 'response' => 1]);
	}
	
	public function actionEditComment($commentId){//dd(func_get_args(), $_POST);
		if($this->db->getOne('Select comment_id from comments where comment_id = ?i', $commentId)){
			$content = $this->textSanitize($_POST['content']);
			$this->db->query('Update comments set comment_content = ?s where comment_id = ?i', $content, $commentId );
			Msg::set(['content' => $content, 'response' => 1]);
		}
		Msg::jsonCode(0);
	}
	
	private function textSanitize($content, $type = 'content', $tagsOn = false){
		$types = [
			'all' => [
				'from' 	=> ['<?', '<?php', '<%'],
				'to' 	=> ['']
			],
			'content' => [
				'from' 	=> [],
				'to' 	=> []
			],
			'title' => [
				'from' 	=> ['\'', '"'],
				'to' 	=> ['’', '»']
			],
		];
		if(!isset($types[$type])) $type = 'content';
		$content = str_replace($types[$type]['from'], $types[$type]['to'], str_replace($types['all']['from'], $types['all']['to'], $content));
		if(!$tagsOn)
			$content = htmlspecialchars($content);
		if($type == 'content')
			$content = html_entity_decode($content);
		
		return $content;
	}
	
	function actionClearCache(){
		if ($objs = glob(CACHE_DIR."*")) {
			foreach($objs as $obj) {
				if(is_dir($obj)){
					do_rmdir($obj);
				}
			}
		}
		exit;
	}
}