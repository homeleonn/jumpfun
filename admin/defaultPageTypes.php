<?php

$this->di->get('config')->addPageType('pages', 
	[
		'type' => 'page',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
	]
);