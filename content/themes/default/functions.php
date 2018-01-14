<?php

// $di->get('config')->addPageType([
		// 'type' => 'new',
		// 'title' => 'Новости',
		// 'description' => 'news1',
		// 'add' => 'Добавить новость',
		// 'edit' => 'Редактировать новость',
		// 'delete' => 'Удалить новость',
		// 'common' => 'новостей',
		// 'hierarchical' => false,
		// 'has_archive'  => true,
		// 'rewrite' => ['slug' => 'news'],
		// 'taxonomy' => [
			// 'newcat' => [
				// 'title' => 'Категории',
				// 'add' => 'Добавить категорию',
				// 'edit' => 'Редактировать категорию',
				// 'delete' => 'Удалить категорию',
				// 'hierarchical' => false,
			// ]
		// ]
// ]);

$di->get('config')->addPageType([
		'type' => 'educator',
		'title' => 'Преподаватели',
		'description' => 'educator1',
		'add' => 'Добавить педагога',
		'edit' => 'Редактировать педагога',
		'delete' => 'Удалить педагога',
		'common' => 'педагогов',
		'hierarchical' => false,
		'has_archive'  => 'educators',
		//'has_archive'  => 'educators',
		'rewrite' => ['slug' => 'educators/%style%', 'with_front' => false, 'paged' => 20],
		'taxonomy' => [
			'style' => [
				'title' => 'Стиль',
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