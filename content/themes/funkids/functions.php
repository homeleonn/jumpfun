<?php

use Jump\DI\DI;
use Jump\helpers\Common;

function funkids_inProgram(){
	?>
	<div class="inline-title">В программу включено</div>
	<div class="row center prog-filling flex line">
		<div><img src="<?=THEME?>img/costumes.jpg" alt="Костюмы аниматоров"><div class="inline-title">Красочные костюмы</div></div>
		<div><img src="<?=THEME?>img/interactive.jpg" alt="Детская интерактивная программа Одесса"><div class="inline-title">Интерактивная программа</div></div>
		<div><img src="<?=THEME?>img/props.jpg" alt="Реквизит на шоу программу, праздник"><div class="inline-title">Реквизит</div></div>
		<div><img src="<?=THEME?>img/musical-equipment.jpg" alt="Музыка, музыкальный реквизит для детских аниматоров в Одессе"><div class="inline-title">Музыкальная аппаратура и сопровождение</div></div>
		<div><img src="<?=THEME?>img/dj.jpg" alt="Диджей, DJ, Ди-джей, музыка на день рождения ребенка"><div class="inline-title">Диджей</div></div>
	</div>
	<?php
}

function funkids_readyToHolyday(){
	?>
	<div class="holyday" id="holyday">
		<h2 class="section-title">Готовимся к празднику уже сейчас</h2>
		<div id="order-question">
			Напишите нам и наш менеджер ответит на все Ваши вопросы
			<div class="inp1">
				<input type="text" id="qname" name="name" placeholder="Имя*">
				<input type="text" id="qtel" name="tel" placeholder="Телефон*">
				<input type="text" id="qmail" name="email" placeholder="Электронная почта">
			</div>
			<div class="captcha-wrapper none center">
				<img alt="captcha" class="captcha pointer captcha-reload">
				<span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span><br>Введите символы с картинки 
				<input type="text" class="captcha-code">
			</div>
			<input type="button" class="button1" id="q-set" value="Отправить">
		</div>
	</div>
	<?php
}

 
function funkids_programPrice($options = null){
	global $post; //d($options, $post);
	if (($options && $options['type'] == 'program' || $options['type'] == 'service') || (isset($post['post_type']) && ($post['post_type'] == 'program' || $post['post_type'] == 'service'))) {
	
	//dd($post);
	?>
	<table class="mtable" cellpadding="2" border="0">
		<tr>
			<td>Стоимость:</td>
			<td><input type="text" name="_jmp_program_price" value="<?=$post['_jmp_program_price'] ?? ''?>"></td>
		</tr>
		<tr>
			<td>Время:</td>
			<td><input type="text" name="_jmp_program_price_time" value="<?=$post['_jmp_program_price_time'] ?? ''?>"></td>
		</tr>
	</table>
	<?php
	}
}

addAction('add_post_after', 'funkids_programPrice', true);
addAction('edit_post_after', 'funkids_programPrice', true);
addFilter('extra_fields_keys', 'funkids_extra_fields_keys');

function funkids_extra_fields_keys($extraFieldKeys){
	$extraFieldKeys = array_merge(
		$extraFieldKeys, 
		['_jmp_program_price', '_jmp_program_price_time']
	);
	
	return $extraFieldKeys;
}

addFilter('single_before_content', 'funkids_single_price');

function funkids_single_price($post){
	if (isset($post['_jmp_program_price']))
		echo '<div class="price">', $post['_jmp_program_price'], (isset($post['_jmp_program_price_time']) ? '/' . $post['_jmp_program_price_time'] : ''), '</div>';
}

addPageType([
		'type' => 'new',
		'title' => 'Блог',
		'_seo_title' => 'Наш блог, статьи, новости | FunKids',
		'h1' => 'Блог',
		'title_for_admin' => 'Блог',
		'description' => 'Последние новости, узнавайте о праздновениях только актуальные новости и статьи | FunKids',
		'add' => 'Добавить запись',
		'edit' => 'Редактировать запись',
		'delete' => 'Удалить запись',
		'common' => 'Записей',
		'hierarchical' => false,
		'has_archive'  => 'blog',
		'rewrite' => ['slug' => 'blog/%newcat%', 'with_front' => false, 'paged' => 20],
		'taxonomy' => [
			'newcat' => [
				'title' => 'Категории',
				'add' => 'Добавить категорию',
				'edit' => 'Редактировать категорию',
				'delete' => 'Удалить категорию',
				'hierarchical' => true,
			]
		]
]);

addPageType([
		'type' => 'service',
		'title' => 'Доп. услуги',
		'_seo_title' => 'Дополнительные услуги | Funkids',
		'h1' => 'Дополнительные услуги',
		'title_for_admin' => 'Доп. услуги',
		'description' => 'Дополнительные услуги на детский праздник, мыльные пузыри, сладкая вата, всё что бы разнообразить праздничный день, запоминающиеся мгновения жизни ребенка | FunKids',
		'add' => 'Добавить услугу',
		'edit' => 'Редактировать услугу',
		'delete' => 'Удалить услугу',
		'common' => 'услуг',
		'hierarchical' => false,
		'has_archive'  => 'services',
		'rewrite' => ['slug' => 'services', 'with_front' => false, 'paged' => 20],
]);

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
		'_seo_title' => 'Детские аниматоры на день рождения Одесса, утренник, выпускной. Пригласить аниматора для ребенка',
		'h1' => 'Аниматоры, шоу программы на детский праздник в Одессе',
		'title_for_admin' => 'Программы',
		'description' => 'Заказать детского аниматора на день рождения ребенка на дом либо на утренник или выпускной в Одессе, широкий выбор аниматоров и шоу программы, а так же красочные детские ведущие, которые порадуют детей интересными конкурсами и подарят массу ярких впечатлений. Все останутся довольны. Заказать аниматора для ребенка на праздник. Цена',
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

function funkidsDate($date){
	$months = [
		'01' => 'января',
		'02' => 'февраля',
		'03' => 'марта',
		'04' => 'апреля',
		'05' => 'мая',
		'06' => 'июня',
		'07' => 'июля',
		'08' => 'августа',
		'09' => 'сентября',
		'10' => 'октября',
		'11' => 'ноября',
		'12' => 'декабря',
	];
	$str = date('d-m-Y', strtotime($date));
	return substr_replace($str, ' ' . $months[substr($str, 3, 2)] . ' ', 2, 4);
}


addFilter('edit_admin_menu', function($sections){
	return array_merge($sections, 
		['Отзывы||comment' => 'reviews']
	);
});

addFilter('before_action_list_args', function($orderBy, $postType){
	if ($postType == 'program' || $postType == 'service') {
		return [['created'], 'ASC'];
	}
});


function isMain(){
	return URI == '/';
}

function resizePhotos(){
	$percent = 0.4;
	foreach(glob(THEME_DIR . '../1/*') as $img){
		$img1 = imagecreatefromjpeg($img);
		// получение новых размеров
		list($width, $height) = getimagesize($img);
		if($width > 900){
			$new_width = $width * $percent;
			$new_height = $height * $percent;

			// ресэмплирование
			$image_p = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($image_p, $img1, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagejpeg($image_p, THEME_DIR . '../3/' . pathinfo($img)['basename'], 50);
		}else{
			imagejpeg($img1, THEME_DIR . '../3/' . pathinfo($img)['basename'], 50);
		}
		
	}
	dd();
}

//resizePhotos();

use frontend\controllers\PostController;


$funKidsCacheFileNames['programs_all'] = 'funkids/programs_all';

function funKids_all(){
	global $funKidsCacheFileNames, $thatCache;
	$thatCache = true;
	if(Common::getCache($funKidsCacheFileNames['programs_all'], -1)) return;
	$popular = (new PostController('program'))->actionList(NULL, NULL, 1, 30,  [['created'], 'ASC'] );
	?>
	<div class="all-progs" id="all-progs">
		<h2 class="section-title">Шоу программы аниматоров</h2>
		<div class="twrapper">
			<div class="row flex">
			<?php foreach($popular['__list'] as $item): ?>
				<div class="item col-md-3 col-sm-6 col-xs-12 center">
				<a href="<?=$item['permalink']?>">
					<div class="img-wrapper">
						<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item['short_title'] ?: $item['title']?>" />
					</div>
					<div class="inline-title"><?=$item['short_title'] ?: $item['title']?></div>
				</a>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['programs_all']);
}


$funKidsCacheFileNames['popular'] = 'funkids/popularHeroes';

function funKids_popular(){
	global $funKidsCacheFileNames;
	if(Common::getCache($funKidsCacheFileNames['popular'], -1)) return;
	$popular = (new PostController('program'))->actionList(NULL, NULL, 1, 5, [['created'], 'ASC']);
	?>
	<div class="popular-progs" id="popular-progs">
		<h2 class="section-title">Популярные программы</h2>
		<div class="carousel-widget container" data-carousel-widget-column="3">
			<div class="widget-head">
				<div class="title"></div>
				<div class="controls">
					<div class="rightside"></div>
					<div class="leftside"></div>
				</div>
			</div>		
			<div class="widget-content">
				<div class="inside-content shower center">
				<?php foreach($popular['__list'] as $item): ?>
					<div class="item"><div class="img"><img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item['short_title'] ?: $item['title']?>" /></div><div class="inline-title"><?=$item['short_title'] ?: $item['title']?></div><?=funkids_clearTags(mb_substr($item['content'], 0 ,200)).'...'?><div><a href="<?=$item['permalink']?>" class="button">Перейти</a></div></div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="center"><a href="<?=uri('programs')?>" class="button">Все программы</a></div>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['popular']);
}


$funKidsCacheFileNames['services'] = 'funkids/services';

function funKids_services(){
	global $funKidsCacheFileNames, $thatCache;
	$thatCache = true;
	if(Common::getCache($funKidsCacheFileNames['services'], -1)) return;
	$services = (new PostController('service'))->actionList(NULL, NULL, 1, 10, [['created'], 'ASC']);
	?>
	<div class="extra-services front-page" id="extra-services">
		<h2 class="section-title"><div>Дополнительные</div> <div>услуги</div></h2>
		<div class="container">
			<h3 class="center">Вы можете заказать дополнительные атрибуты к празднику, которые оставят незабываемые впечатления!</h3>
			<div class="flex">
			<?php foreach($services['__list'] as $item): ?>
				<div class="item">
					<a href="<?=$item['permalink']?>">
						<div class="img2">
							<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item['short_title'] ?: $item['title']?>" />
						</div>
						<div class="inline-title center"><?=$item['short_title'] ?: $item['title']?></div>
					</a>
				</div>
			<?php endforeach; ?>
			</div>
			<div class="center"><a href="<?=uri('services')?>" class="button">Все доп. услуги</a></div>
		</div>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['services']);
}


$funKidsCacheFileNames['like'] = 'funkids/like';
function funKids_like($id){
	global $funKidsCacheFileNames, $thatCache;
	$thatCache = true;
	if(Common::getCache($funKidsCacheFileNames['like'].$id, 86400)) return;
	$popular = (new PostController('program'))->actionList(NULL, NULL, 1, 5, [['id'], ['DESC', 'ASC'][mt_rand(0, 1)]]);
	shuffle($popular['__list']);
	//dd($popular['__list']);
	?>
	<div>
		<div class="carousel-widget container" data-carousel-widget-column="3">
			<div class="widget-head">
				<div class="title">Похожие программы аниматоров</div>
				<div class="controls">
					<div class="rightside"></div>
					<div class="leftside"></div>
				</div>
			</div>		
			<div class="widget-content">
				<div class="inside-content center">
				<?php 
					foreach($popular['__list'] as $item): 
					if($item['id'] == $id) continue; 
				?>
					<article class="item">
						<div class="img2">
							<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item['short_title'] ?: $item['title']?>" />
						</div>
						<h1 class="inline-title"><?=$item['title']?></h1>
						<?=funkids_clearTags(mb_substr($item['content'], 0 ,200)).'...'?>
						<div><a href="<?=$item['permalink']?>" class="button">Перейти</a></div>
					</article>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="center"><a href="<?=uri('programs')?>" class="button">Все программы</a></div>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['like'].$id);
}


$funKidsCacheFileNames['catalog'] = 'funkids/catalogOfHeroes';

function funKids_catalogHeroes(){
	global $funKidsCacheFileNames, $thatCache;
	$thatCache = true;
	if(Common::getCache($funKidsCacheFileNames['catalog'], -1)) return;
	$heroes = (new PostController('program'))->actionList(NULL, NULL, 1, 30, [['created'], 'ASC']);
	$heroes = array_reverse($heroes);
	$heroesImgs = [];
	foreach($heroes['__list'] as $h){
		$heroesImgs[] = postImgSrc($h, 'medium');
		?>
		<article class="item"><a href="<?=$h['permalink']?>"><?=$h['short_title'] ?: $h['title']?></a>
			<div class="preview center">
				<noscript><img src="<?=postImgSrc($h, 'medium')?>" alt="<?=$h['title']?>" /></noscript>
				<div class="inline-title"><h1><?=$h['short_title'] ?: $h['title']?></h1></div><?=funkids_clearTags(mb_substr($h['content'], 0 ,200)).'...'?>
			</div>
		</article>
		<?php
	}
	?>
	<script>
		$$(function(){
			var heroesImgs = <?=json_encode($heroesImgs)?>;
			$('.heroes-catalog .list').one('mouseover', function(){
				$('.heroes-catalog > .list > article > .preview').each(function(i){
					$(this).prepend('<img src="'+heroesImgs[i]+'">');
				});
			});
		});
	</script>
	<?php
	echo Common::setCache($funKidsCacheFileNames['catalog']);
}

function funkids_clearTags($text){
	return preg_replace(['/<[^\s]*>/', '/<.*/', '/.*>/'], '', $text);
}


function funKidsUploadedImgPath($path, $thumb = false){
	$pathParts = pathinfo($path);
	$imgPath = $pathParts['dirname'] . '/' . $pathParts['filename'] . ($thumb ? '-150x150' : '') . '.' . $pathParts['extension'];
	return UPLOADS . (is_file(UPLOADS_DIR . $imgPath) ? $imgPath : $path);
}


$funKidsCacheFileNames['reviews'] = 'funkids/reviews';

function funkids_getLastReviews(){
	global $funKidsCacheFileNames;
	if(Common::getCache($funKidsCacheFileNames['reviews'], -1)) return;
	$reviews = DI::getD('db')->getAll('Select * from reviews where status = 1 order by id DESC limit 10');
	?>
	<div class="reviews topoffset" id="reviews">
		<div class="carousel-widget container" data-carousel-widget-column="2">
			<div class="widget-head">
				<div class="title"><h2>Последние отзывы наших клиентов</h2></div>
				<div class="controls">
					<div class="rightside"></div>
					<div class="leftside"></div>
				</div>
			</div>
			<div class="widget-content">
				<div class="inside-content">
				<?php foreach($reviews as $review): ?>
					<div class="item">
						<div class="floatimg sprite reviewimg"></div>
						<p class="quote-big">
							<?=$review['text']?>
						</p>
						<div class="right fs22"><?=$review['name']?></div>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="container center">
		<a href="<?=uri('reviews')?>" class="button">Перейти ко всем отзывам</a>
		<a href="#" class="button get-review-form">Оставить отзыв</a>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['reviews']);
}


function funClearCache($post){//dd(getPageOptionsByType($post['post_type']));
	global $funKidsCacheFileNames;
	$postOptions = getPageOptionsByType($post['post_type']);
	
	if($post['post_type'] == 'program'){
		//Common::clearCache($funKidsCacheFileNames['popular']);
		Common::clearCache($funKidsCacheFileNames['catalog']);
		Common::clearCache($funKidsCacheFileNames['programs_all']);
		Common::clearCache('pages/' . getOption('front_page'));
	}
	elseif($post['post_type'] == 'service'){
		Common::clearCache($funKidsCacheFileNames['services']);
		Common::clearCache('pages/' . getOption('front_page'));
	}
	
	if(isset($post['id']) && $post['id'] == getOption('front_page')){
		Common::clearCache('pages/' . $post['id']);
	}
	
	if(isset($post['url'])){
		$preSlug = !$postOptions['rewrite']['with_front'] ? $postOptions['rewrite']['slug'] . '/' : '';
		Common::clearCache('pages/' . md5($preSlug . $post['url']));
	}
	
	if(!$postOptions['hierarchical']){
		Common::clearCache('pages/list-' . $post['post_type']);
	}
	
}

addAction('before_post_add', 'funClearCache');
addAction('before_post_edit', 'funClearCache');
addAction('before_post_delete', 'funClearCache');
addAction('reviewDelete', 'funClearCacheReviews');
addAction('reviewToggle', 'funClearCacheReviews');

function funClearCacheReviews(){
	Common::clearCache('funkids/reviews');
	Common::clearCache('pages/' . getOption('front_page'));
}

addAction('before_single_page', function($id){
	global $funkidsFileCacheName;
	if($id != getOption('front_page')) return;
	$funkidsFileCacheName = 'pages/' . $id;
	if(Common::getCache($funkidsFileCacheName, -1)) return -2;
});

addAction('after_rendering', function(){
	global $funkidsFileCacheName;
	if($funkidsFileCacheName)
		echo Common::setCache($funkidsFileCacheName);
});



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
				<div>Ответы (<?=$subCommentsCount?>)</div>
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