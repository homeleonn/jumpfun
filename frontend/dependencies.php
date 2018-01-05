<?php

return [
	'controllers' => [
		
	],
	'models' => [
		'\frontend\models\Post\Post' => [
			'\frontend\models\Post\Taxonomy' => [
				'di' => [
					'db'
				]
			]
		],
		'\frontend\models\Category\Category' => [
		],
	],
];