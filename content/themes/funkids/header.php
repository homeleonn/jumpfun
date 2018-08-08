<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?=$title;?></title>
	<link rel="stylesheet" href="<?=THEME?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=THEME?>css/fontello.css">
	<link rel="stylesheet" href="<?=THEME?>css/front-slider.css">
	<link rel="stylesheet" href="<?=THEME?>css/style.css">
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600" rel="stylesheet" type="text/css">
	<script src="<?=THEME?>js/jq3.js"></script>
	<script src="<?=THEME?>js/js.js"></script>
	<script src="<?=THEME?>js/mail.js"></script>
	<?php jmpHead() ?>
	<link rel="shortcut icon" href="<?=ROOT_URI?>favicon.ico" type="image/x-icon">
</head>
<body>
	<div class="wrapper<?=isMain()?' main':''?>">
		<div class="header">
			<div class="top-line"></div>
			<div class="top-sky">
				<div class="clouds"></div>
				<div class="air-balloons-left"></div>
				<div class="air-balloons-right"></div>
			</div>
			<div class="header-content">
				<div class="container">
					<div class="social">
						<a href="#" rel="nofollow"><div class="facebook icon-facebook-1"></div></a>
						<a href="#" rel="nofollow"><div class="twitter icon-twitter"></div></a>
						<a href="#" rel="nofollow"><div class="youtube icon-youtube"></div></a>
						<a href="#" rel="nofollow"><div class="gplus icon-gplus"></div></a>
					</div>
					<div class="logo-text">
						<a href="#">
							<div class="first-word">Fun</div>
							<div class="second-word">Kids</div>
						</a>
					</div>
					<?php getMenu() ?>
					<?php if(isMain()):?>
					<!--SLIDER(+-)-->
					<div class="slider-wrapper">
						<div class="slider">
							<div class="ss">
								<div class="item active">
									<img src="<?=THEME?>img/front-slider/2.jpg" alt="" />
									<div class="slider-title">
										<div>Детские праздники Одесса</div>
										<div>Широкий выбор праздничных программ на любой вкус</div>
									</div>
								</div>
								
								<div class="item">
									<img src="<?=THEME?>img/front-slider/1.jpg" alt="" />
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
		