<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php jmpHead() ?>
	<link rel="stylesheet" href="<?=THEME?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=THEME?>css/fontello.css">
	<link rel="stylesheet" href="<?=THEME?>css/front-slider.css">
	<link rel="stylesheet" href="<?=THEME?>css/style.css">
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600" rel="stylesheet" type="text/css">
	<script src="<?=THEME?>js/jq3.js"></script>
	<link rel="shortcut icon" href="<?=ROOT_URI?>favicon.ico" type="image/x-icon">
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
				<div class="clouds"></div>
				<div class="air-balloons-left"></div>
				<div class="air-balloons-right"></div>
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
							<a href="<?=SITE_URL?>"><img src="<?=THEME?>img/logo_trnsprnt1.png" alt="Логотип. Организация детских праздников Одесса. Аниматоры. Шоу программы. Низкие цены"></a>
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
					<div class="slider-wrapper">
						<div class="slider">
							<div class="ss">
								<div class="item active">
									<iframe width="1120" height="385" src="https://www.youtube.com/embed/ZfZfm1twto8" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
								</div>
								<div class="item">
									<img src="<?=THEME?>img/front-slider/1.jpg" alt="Красивые картинки детский праздник Одесса" />
									<div class="slider-title">
										<div>Детские праздники Одесса 2</div>
										<div>Широкий выбор праздничных программ на любой вкус</div>
									</div>
								</div>
							</div>
							<div class="controls">
								<div class="arr-left"></div>
								<div class="arr-right"></div>
							</div>
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
		