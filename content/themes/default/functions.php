<?php

// addPageType([
		// 'type' => 'new',
		// 'title' => 'Новости',
		// 'title_for_admin' => 'Новости',
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

addPageType([
		'type' => 'educator',
		'title' => 'Преподаватели академии',
		'title_for_admin' => 'Преподаватели',
		'description' => 'Наши преподаватели, они помогут вам освоить хореографию.',
		'add' => 'Добавить педагога',
		'edit' => 'Редактировать педагога',
		'delete' => 'Удалить педагога',
		'common' => 'педагогов',
		'hierarchical' => false,
		'has_archive'  => 'educators',
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




// можешь придумать свой тип записи, например: врачи, игрушки
// addPageType([
		// 'type' => 'doctor', // doctor / toy
		// 'title' => 'Врачи',
		// 'title_for_admin' => 'Врачи',
		// 'description' => 'news1', // Медицинский доктор — исторические: лекарь, цирюльник, лейб-медик, фельдшер, доктор и др.; современный — врач. См. также медицинские профессии.
		// 'add' => 'Добавить врача',
		// 'edit' => 'Редактировать врача',
		// 'delete' => 'Удалить врача',
		// 'common' => 'врачей',
		// 'hierarchical' => false, // это позже если что объясню
		// 'has_archive'  => 'doctors', // страница на которой будут отображаться все доктора / игрушки
		// 'rewrite' => ['slug' => 'doctors/%doc-category%'], // slug будет добавлен к ссылке на странице конкретного врача (реализовано, но пока еще не гибко) doctors/%doc-category% , doc-category нужно будет добавить ниже в таксономию, тоже пока не гибко. Протестируй, в админке сразу появится новый пункт меню и можно попробовать.
		// 'taxonomy' => [
			// 'doc-category' => [
				// 'title' => 'Категории',
				// 'add' => 'Добавить категорию',
				// 'edit' => 'Редактировать категорию',
				// 'delete' => 'Удалить категорию',
				// 'hierarchical' => true,
			// ]
		// ]
// ]);



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