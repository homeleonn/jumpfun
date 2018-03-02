<?php

return [
	['', 'dashboard', 'method' => 'GET'],
	['media', 'media/show', 'GET'],
	['media/async', 'media/show/async', 'GET'],
	['media/add', 'media/add', 'POST'],
	['media/del/(\d+)', 'media/del/$1', 'POST'],
	['user', 'user/list', 'GET'],
	['delComment/(\d+)', 'user/delComment/$1', 'POST'],
	// ['categories', 'category/list', 'method' => 'GET'],
	// ['products', 'products/list', 'method' => 'GET'],
];