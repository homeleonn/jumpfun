<?php

$this->di->get('config')->addPageType([
		'slug' => 'pages',
		'type' => 'page',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
		'hierarchical' => true,
		'taxonomy' => [],
]);