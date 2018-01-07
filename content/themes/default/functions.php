<?php

// $di->get('config')->addPageType([
		// 'slug' => 'news',
		// 'type' => 'new',
		// 'title' => 'Новости',
		// 'description' => 'news1',
		// 'add' => 'Добавить новость',
		// 'edit' => 'Редактировать новость',
		// 'delete' => 'Удалить новость',
		// 'common' => 'новостей',
		//'hierarchical' => false,
// ]);

$di->get('config')->addPageType([
		'slug' => 'educators',
		'type' => 'educator',
		'title' => 'Преподаватели',
		'description' => 'educator1',
		'add' => 'Добавить педагога',
		'edit' => 'Редактировать педагога',
		'delete' => 'Удалить педагога',
		'common' => 'педагогов',
		'hierarchical' => false,
		'taxonomy' => [
			'style' => [
				'title' => 'Стиль педагога',
				'add' => 'Добавить стиль',
				'edit' => 'Редактировать стиль',
				'delete' => 'Удалить стиль',
				'hierarchical' => false,
			],
			'age' => [
				'title' => 'Возрастная категория',
				'add' => 'Добавить возрастную категорию',
				'edit' => 'Редактировать возрастную категорию',
				'delete' => 'Удалить возрастную категорию',
				'hierarchical' => false,
			],
		]
]);