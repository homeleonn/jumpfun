<?php

return [
	'controllers' => [
		
	],
	'models' => [
		'\frontend\models\Post\Post' => [
			'di',
			'\frontend\models\Post\Taxonomy' => [
				'di' => [
					'db'
				]
			]
		],
		'\frontend\models\Category\Category' => [
			'di'
		],
	],
];