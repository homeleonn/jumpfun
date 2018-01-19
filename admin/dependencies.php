<?php

return [
	'controllers' => [
		
	],
	'models' => [
		'\admin\models\Post\Post' => [
			'\frontend\models\Post\Taxonomy' => [
				'di' => [
					'db'
				]
			]
		],
		'\admin\models\Dashboard\Dashboard' => [
		],
	],
];