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
function themeHTMLCommentTable($comment, $subComments = NULL, $level = 1){
	ob_start();
	?>
	<table <?=!$level ? 'class="general"' : ''?> data-id="<?=$comment['comment_id']?>" data-author="<?=$comment['comment_author']?>"  data-parent="<?=$comment['comment_parent']?>">
		<tr>
			<td rowspan="3"><div class="avatar"></div></td>
		</tr>
		<tr>
			<td class="comment-author icon-"><?=$comment['comment_author']?></td>
			<td width="100%"><?=substr($comment['comment_date'], 0, -3)?></td>
			<?php if(isAdmin() && inAdmin()):?>
			<td><span class="icon-cancel" id="comment-delete" title="Удалить"></span></td>
			<?php endif;?>
			<td><span class="icon-comment" title="Ответить"></span></td>
		</tr>
		<tr>
			<td colspan="3" class="msg">
				<?=$comment['comment_content']?>
			</td>
		</tr>
		<?php if($subComments): $subCommentsCount = count($subComments); ?>
		<tr>
			<td colspan="3" class="sub-comments">
				<div style="">Ответы (<?=$subCommentsCount?>)</div>
				<?php
					foreach(array_reverse($subComments) as $subComment){
						echo themeHTMLCommentTable($subComment);
					}
				?>
			</td>
		</tr>
		<?php endif; ?>
	</table>
	<?php
	return ob_get_clean();
}