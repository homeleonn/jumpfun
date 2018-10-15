<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php jmpHead() ?>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&subset=latin,cyrillic" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=THEME?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=THEME?>css/fontello.css">
	<link rel="stylesheet" href="<?=THEME?>css/style.css">
	<link rel="shortcut icon" href="<?=ROOT_URI?>favicon.ico" type="image/x-icon">
	<script>function $$(callback){window.addEventListener('load', callback);}</script>
</head>
<body>
	<div class="wrapper<?=isMain()?' main':''?>" id="wrapper">
		<div class="header">
			<?php if(isMain()):?>
			<div class="front-menu flex line">
				<a href="#all-progs">Шоу программы</a>¤
				<a href="#extra-services">Доп. услуги</a>¤
				<a href="#s-about">О нас</a>¤
				<a href="#holyday">Обратная связь</a>¤
				<a href="#reviews">Отзывы</a>¤
				<a href="#wrapper">Вверх</a>
				<div class="bottom-phone"><span class="icon-phone" title="Заказать обратный звонок"></span> +38(067) 797-93-85</div>
			</div>
			<?php endif;?>
			<div class="top-line"></div>
			<div class="top-sky">
				<div class="clouds sprite"></div>
				<div class="air-balloons-left sprite"></div>
				<div class="air-balloons-right sprite"></div>
			</div>
			<div class="header-content">
				<div class="container">
					<div class="social">
						<a href="https://www.facebook.com/profile.php?id=100004157191483&refsrc=https%3A%2F%2Fm.facebook.com%2Ffbrdr%2F2048%2F100004157191483%2F" rel="nofollow" target="_blank"><div class="facebook icon-facebook-1"></div></a>
						<a href="https://www.youtube.com/channel/UChNlyGg0f5F-mDHxy8UP8IQ" rel="nofollow" target="_blank"><div class="youtube icon-youtube"></div></a>
						<a href="https://www.instagram.com/funkids_odessa/" rel="nofollow" target="_blank"><div class="instagram icon-instagram"></div></a>
						<a href="https://vk.com/club163464318" rel="nofollow" target="_blank"><div class="vk icon-vk"></div></a>
					</div>
					<div class="row">
						<div class="logo-text">
							<a href="<?=SITE_URL?>"><img alt="Логотип. Организация детских праздников Одесса. Аниматоры. Шоу программы. Низкие цены" src="<?=THEME?>img/logo_trnsprnt1.png"></a>
						</div>
						<div class="tels fs25">
							<a href="tel:+380677979385">(067) 797-93-85</a>
							<a href="tel:+380632008595">(063) 200-85-95</a><br>
							<div class="phone-top"><span class="icon-phone"></span><span>Заказать обратный звонок</span></div>
						</div>
					</div>
					<?php getMenu() ?>
					<?php if(isMain()):?>
					<!--SLIDER(+-)-->
					<script>
						$$(function(){
							let sliderImgs = ['51.jpg', '52.jpg', '53.jpg'];
							setTimeout(function(){
								$('.slider > .ss > .item:not(.active)').each(function(i){
									$(this).prepend('<img src="<?=THEME?>img/1x1.gif" data-src="<?=THEME?>img/mini-gallery/'+sliderImgs[i]+'" />');
								});
							}, 500);
						});
					</script>
					<div class="slider-wrapper">
						<div class="slider">
							<div class="ss">
								<div class="item active">
									<div class="yout"><div class="yplay">&#9658;</div><img data-youtube="ZfZfm1twto8" src="<?=THEME?>img/mini-gallery/50.jpg" alt="Аниматор трансформер Оптимус Прайм на детский праздник Одесса"></div>
								</div>
								<div class="item">
									<div class="slider-title">
										<div>Праздник дня рождения</div>
										<div>Веселые ребята и их родители с нашими аниматорами</div>
									</div>
								</div>
								<div class="item">
									<div class="slider-title">
										<div>Детский праздник в Одессе</div>
										<div>Аниматоры человек-паук и Оптимус Прайм, дети остаются довольными</div>
									</div>
								</div>
								<div class="item">
									<div class="slider-title">
										<div>Бэтмен на защите праздника</div>
										<div>Детская шоу программа Бэтмена - яркие впечатления детей</div>
									</div>
								</div>
							</div>
							<div class="controls">
								<div class="arr-left"></div>
								<div class="arr-right"></div>
							</div>
							<div class="progressbar"></div>
						</div>
					</div>
					<?php endif;?>
				</div>
			</div>
		</div>
		
		<div class="content">
			<div class="container-fluid breadcrumbs">
				<div class="container"><?=getBreadCrumbs();?></div>
			</div>
		