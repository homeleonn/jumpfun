<?php

return [
	['', 'dashboard', 'method' => 'GET'],
	['', 'dashboard/save', 'method' => 'POST'],
	
	['media', 'media/show', 'GET'],
	['media/async', 'media/show/async', 'GET'],
	['media/add', 'media/add', 'POST'],
	['media/del/(\d+)', 'media/del/$1', 'POST'],
	
	['users', 'user/list', 'GET'],
	['user/clearcache', 'user/clearCache', 'POST'],
	
	['comments', 'post/commentsList', 'GET'],
	['delComment/(\d+)', 'user/delComment/$1', 'POST'],
	['comment-edit/(\d+)', 'user/editComment/$1', 'POST'],
	
	['menu', 'menu', 'GET'],
	['menu', 'menu', 'POST'],
	['menu/edit', 'menu/edit', 'POST'],
	['menu/select', 'menu/select', 'POST'],
	['menu/activate', 'menu/activate', 'POST'],
	
	['plugins', 'plugin', 'GET'],
	['plugins/toggle/(.*)/(.*)', 'plugin/toggle/$1/$2', 'GET'],
	
	
	['settings', 'setting', 'GET'],
	['settings/save', 'setting/save', 'POST'],
	
	// ['categories', 'category/list', 'method' => 'GET'],
	// ['products', 'products/list', 'method' => 'GET'],
	
	['reviews', 'review', 'GET'],
	['review/delete/(\d+)', 'review/delete/$1', 'GET'],
	['review/toggle/(\d+)', 'review/toggle/$1', 'GET'],
];