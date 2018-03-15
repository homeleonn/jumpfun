<?php

return [
	['', 'dashboard', 'method' => 'GET'],
	['media', 'media/show', 'GET'],
	['media/async', 'media/show/async', 'GET'],
	['media/add', 'media/add', 'POST'],
	['media/del/(\d+)', 'media/del/$1', 'POST'],
	['users', 'user/list', 'GET'],
	['comments', 'post/commentsList', 'GET'],
	['delComment/(\d+)', 'user/delComment/$1', 'POST'],
	['comment-edit/(\d+)', 'user/editComment/$1', 'POST'],
	['menu', 'menu', 'GET'],
	['menu/edit', 'menu/edit', 'POST'],
	// ['categories', 'category/list', 'method' => 'GET'],
	// ['products', 'products/list', 'method' => 'GET'],
];