<?php

$this->di->get('config')->addPageType([
		'type' => 'page',
		'title' => 'Страницы',
		'description' => 'Страницы',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
		'hierarchical' => true,
		'has_archive'  => false,
		'taxonomy' => [],
		'rewrite' => ['slug' => 'pages', 'with_front' => true, 'paged' => false],
]);