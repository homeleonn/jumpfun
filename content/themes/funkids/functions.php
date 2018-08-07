<?php

use Jump\DI\DI;
use Jump\helpers\Common;



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

// addPageType([
		// 'type' => 'educator',
		// 'title' => 'Преподаватели академии',
		// 'title_for_admin' => 'Преподаватели',
		// 'description' => 'Наши преподаватели, они помогут вам освоить хореографию.',
		// 'add' => 'Добавить педагога',
		// 'edit' => 'Редактировать педагога',
		// 'delete' => 'Удалить педагога',
		// 'common' => 'педагогов',
		// 'hierarchical' => false,
		// 'has_archive'  => 'educators',
		// 'rewrite' => ['slug' => 'educators/%style%', 'with_front' => false, 'paged' => 20],
		// 'taxonomy' => [
			// 'style' => [
				// 'title' => 'Стиль',
				// 'add' => 'Добавить стиль',
				// 'edit' => 'Редактировать стиль',
				// 'delete' => 'Удалить стиль',
				// 'hierarchical' => true,
			// ],
			// 'age' => [
				// 'title' => 'Возрастная категория',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
		// ]
// ]);

addPageType([
		'type' => 'program',
		'title' => 'Программы',
		'h1' => 'Программы детских праздников в Одессе',
		'title_for_admin' => 'Программы',
		'description' => 'Программы',
		'add' => 'Добавить программу',
		'edit' => 'Редактировать программу',
		'delete' => 'Удалить программу',
		'common' => 'программ',
		'hierarchical' => false,
		'has_archive'  => 'programs',
		'rewrite' => ['slug' => 'programs', 'with_front' => false, 'paged' => 20],
		// 'taxonomy' => [
			// 'age' => [
				// 'title' => 'Возрастная категория',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
		// ]
]);


addFilter('edit_admin_menu', function($sections){
	return array_merge($sections, 
		['Отзывы||comment' => 'reviews']
	);
});


function isMain(){
	return URI == '/';
}

use frontend\controllers\PostController;

$funKidsCacheFileNames['popular'] = 'funkids/popularHeroes';

function funKids_popular(){
	global $funKidsCacheFileNames;
	if(Common::getCache($funKidsCacheFileNames['popular'], -1)) return;
	$popular = (new PostController('program'))->actionList(NULL, NULL, 1, 4, [['visits'], 'DESC']);
	?>
	<div class="popular-progs">
		<div class="carousel-widget container" data-carousel-widget-column="3">
			<div class="widget-head">
				<div class="title">Популярные программы</div>
				<div class="controls">
					<div class="rightside"></div>
					<div class="leftside"></div>
				</div>
			</div>		
			<div class="widget-content">
				<div class="inside-content shower center">
				<?php foreach($popular['__list'] as $item): ?>
					<div class="item"><div class="img"><img src="<?=UPLOADS . $item['_jmp_post_img']?>" alt="<?=$item['title']?>" /></div><div class="inline-title"><?=$item['title']?></div><?=funkids_clearTags(mb_substr($item['content'], 0 ,200)).'...'?><div><a href="<?=$item['permalink']?>" class="button">Перейти</a></div></div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="center"><a href="<?=uri('programs')?>" class="button">Все программы</a></div>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['popular']);
}

$funKidsCacheFileNames['catalog'] = 'funkids/catalogOfHeroes';

function funKids_catalogHeroes(){
	global $funKidsCacheFileNames;
	if(Common::getCache($funKidsCacheFileNames['catalog'])) return;
	$heroes = (new PostController('program'))->actionList();
	foreach($heroes['__list'] as $h){
		?><div class="item"><a href="<?=$h['permalink']?>"><?=$h['title']?></a></div><?php
	}
	echo Common::setCache($funKidsCacheFileNames['catalog']);
}

function funkids_clearTags($text){
	//return preg_replace('/<[^\s]*/', '', preg_replace('/<[^\s]*>/', '', $text));
	return preg_replace(['/<[^\s]*>/', '/<.*/', '/.*>/'], '', $text);
}


$funKidsCacheFileNames['reviews'] = 'funkids/reviews';

function funkids_getLastReviews(){
	global $funKidsCacheFileNames;
	if(Common::getCache($funKidsCacheFileNames['reviews'])) return;
	$reviews = DI::getD('db')->getAll('Select * from reviews where status = 1 order by id DESC limit 3');
	?>
	<section class="reviews topoffset">
				<div class="carousel-widget container" data-carousel-widget-column="2">
					<div class="widget-head">
						<div class="title">Последние отзывы наших клиентов</div>
						<div class="controls">
							<div class="rightside"></div>
							<div class="leftside"></div>
						</div>
					</div>
					<div class="widget-content">
						<div class="inside-content">
						<?php foreach($reviews as $review): ?>
							<div class="item">
								<div class="floatimg">
									<img src="<?=THEME?>img/review.jpg" alt="Отзыв клиента <?=$review['name']?>" />
								</div>
								<p class="quote-big">
									<?=$review['text']?>
								</p>
								<div class="right fs22"><?=$review['name']?></div>
							</div>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
			</section>
	<?php
	echo Common::setCache($funKidsCacheFileNames['reviews']);
}


function funClearCache($post){
	if($post['post_type'] == 'program'){
		global $funKidsCacheFileNames;
		Common::clearCache($funKidsCacheFileNames['popular']);
		Common::clearCache($funKidsCacheFileNames['catalog']);
	}
}

addAction('before_post_add', 'funClearCache');
addAction('before_post_edit', 'funClearCache');
addAction('before_post_delete', 'funClearCache');




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