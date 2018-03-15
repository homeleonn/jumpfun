<!DOCTYPE html>
<html lang="ru">
<head>
	<title><?=$title;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="<?=THEME?>css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?=THEME?>css/fontello.css" />
	<link rel="stylesheet" type="text/css" href="<?=THEME?>css/style1.css" />
	<link rel="stylesheet" type="text/css" href="<?=THEME?>css/new.css" />
	<!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600" rel="stylesheet" type="text/css">-->
	<?php jmpHead() ?>
	<link rel="shortcut icon" href="<?=THEME?>favicon.ico" type="image/x-icon">
</head>
<body>
	<!--HEADER(+-)-->
	<header style="height: 250px;">
		<!--<div id="head">
			<div class="head-bg1 active"></div>
			<div class="head-bg2 "></div>
			<div class="head-bg4 "></div>
		</div>-->
		<div id="header-feature"></div>
		<div id="top">
			<div id="contacts">
				<ul>
					<li><img src="<?=THEME?>img/target1.png" /> г.Одесса, ул.Лейтенанта Шмидта, 5</li>
					<li><img src="<?=THEME?>img/time1.png" /> Будние дни с 10:00 до 18:00</li>
					<li><img src="<?=THEME?>img/mail1.png" /> 15diva@mail.ru</li>
					<li><img src="<?=THEME?>img/phone1.png" /> +38 (050) 333-48-08</li>
				</ul>
				<div><a href="<?=SITE_URL?>user/<?=(!isAuthorized() ? 'login/' : '')?>"><span class="icon-user" style="font-size:35px; margin-right: 20px;float: right;"></span></a></div>
			</div>
			<div id="call-top" class="b i small1">
				Закажите бесплатный звонок для уточнения информации: <input type="text" id="call" placeholder="Ваш номер телефона"> <input type="button" id="head-call" value="Заказать звонок">
			</div>
			<div style="position: relative;">
				<div class="social1">
					<a href="https://vk.com/limuzin_arenda_odessa" target="_blank" class="logo-vk" title="Мы Вконтакте"></a>
					<a href="https://www.facebook.com/%D0%90%D1%80%D0%B5%D0%BD%D0%B4%D0%B0-%D0%BF%D1%80%D0%BE%D0%BA%D0%B0%D1%82-%D0%BB%D0%B8%D0%BC%D1%83%D0%B7%D0%B8%D0%BD%D0%BE%D0%B2-%D0%B0%D0%B2%D1%82%D0%BE%D0%B1%D1%83%D1%81%D0%BE%D0%B2-%D0%B2-%D0%9E%D0%B4%D0%B5%D1%81%D1%81%D0%B5--196986637301447" target="_blank" class="logo-fb" title="Мы В Фейсбук"></a>
					<a href="https://ok.ru/group/54674360107021" target="_blank" class="logo-ok" title="Мы В Однокласниках"></a>
					<a href="https://plus.google.com/u/0/+%D0%94%D0%98%D0%92%D0%90%D0%B0%D0%B2%D1%82%D0%BE%D0%B1%D1%83%D1%81%D1%8B%D0%B8%D0%BB%D0%B8%D0%BC%D1%83%D0%B7%D0%B8%D0%BD%D1%8B%D0%9E%D0%B4%D0%B5%D1%81%D0%B0/posts" target="_blank" class="logo-google" title="Наша страничка в Google Plus G+"></a>
					<a href="https://www.youtube.com/channel/UCsyUe2_NR6jjFCDSBJbHZnw" target="_blank" class="logo-youtube" title="Смотрите нас на Youtube"></a>
					<a href="https://www.instagram.com/15diva/" target="_blank" class="logo-insta" title="Инстаграм"></a>
				</div>
				<nav>
					<?php getMenu() ?>
					<!--<nav>
						<ul id="menu-main" class="menu">
							<li><a href="<?=SITE_URL?>">Главная</a></li>
							<li><a href="<?=SITE_URL?>javascript/">JavaScript</a></li>
							<li><a href="<?=SITE_URL?>sedans/">Седаны</a></li>
							<li><a href="<?=SITE_URL?>educators/">Преподаватели</a></li>
							<li><a href="<?=SITE_URL?>news/">Новости</a></li>
							<li><a href="<?=SITE_URL?>category/news/">Новости</a></li>
							<li><a href="<?=SITE_URL?>eli-c2/">Ели</a></li>
						</ul>			
					</nav>-->
					<div id="hide-nav"><img src="<?=THEME?>img/hide-nav.png"></div>
				</nav>
			</div>
		</div>
		<div id="head-text">
			<div>
				<h1>Аренда лимузинов, седанов и микроавтобусов в Одессе</h1>
			</div>
			<!--<button class="btn-tr b i">О нас</button>-->
		</div>
		<div id="main-logo"><img alt="Транспортная компания Дива в Одессе. Лимузины, седаны, микроавтобусы" src="<?=THEME?>img/logo2.png"></div>
		<div id="sec-logo"><img src="<?=THEME?>img/second-logo.jpg"></div>
	</header>
	<div class="container-fluid">
	<?=$this->di->get('config')->getBreadCrumbs();?>