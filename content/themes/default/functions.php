<?php

$di->get('config')->addPageType('news', 
	[
		'type' => 'new',
		'title' => 'Новости',
		'description' => 'news1',
		'add' => 'Добавить новость',
		'edit' => 'Редактировать новость',
		'delete' => 'Удалить новость',
		'common' => 'новостей',
	]
);

$di->get('config')->addPageType('educators', 
	[
		'type' => 'educator',
		'title' => 'Преподаватели',
		'description' => 'educator1',
		'add' => 'Добавить педагога',
		'edit' => 'Редактировать педагога',
		'delete' => 'Удалить педагога',
		'common' => 'педагогов',
	]
);

$di->get('config')->addPageType('events', 
	[
		'type' => 'event',
		'title' => 'Мероприятия',
		'description' => 'Мероприятия',
		'add' => 'Добавить Мероприятие',
		'edit' => 'Редактировать Мероприятие',
		'delete' => 'Удалить Мероприятие',
		'common' => 'мероприятия',
	]
);