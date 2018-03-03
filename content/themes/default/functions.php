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
		// 'has_archive'  =>'news',
		// 'rewrite' => ['slug' => 'news/%newcat%'],
		// 'taxonomy' => [
			// 'newcat' => [
				// 'title' => 'Категории',
				// 'add' => 'Добавить категорию',
				// 'edit' => 'Редактировать категорию',
				// 'delete' => 'Удалить категорию',
				// 'hierarchical' => true,
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
				'hierarchical' => true,
			],
			'age' => [
				'title' => 'Возрастная категория',
				'add' => 'Добавить возрастную категорию',
				'edit' => 'Редактировать возрастную категорию',
				'delete' => 'Удалить возрастную категорию',
				'hierarchical' => true,
			],
		]
]);
function themeHTMLCommentTable($comment, $commentCount = NULL){
	ob_start();
	?>
	<table>
		<tr>
			<td class="address"><?=$comment['comment_author']?></td>
			<td width="100%"><?=$comment['comment_date']?></td>
			<td><span class="icon-comment" title="Ответить"></span></td>
			<td><?=$commentCount ? '№' . $commentCount : ''?></td>
		</tr>
		<tr>
			<td colspan="5"><?=$comment['comment_content']?></td>
		</tr>
	</table>
	<?php
	return ob_get_clean();
}