<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Панель администратора</title>
	<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_THEME;?>css/fontello.css">
	<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_THEME;?>css/style.css">
	<link rel="stylesheet" type="text/css" href="<?php echo ADMIN_THEME;?>css/style1.css">
	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600"/>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo ADMIN_THEME;?>img/favicon.ico"/>
	<script src="<?=SITE_URL?>admin/view/js/jq3.js"></script>
	<script src="<?=SITE_URL?>admin/view/js/admin.js"></script>
	<?php //ju_head()?>
</head>
<body>
<div id="wrapper">
	<header>
		<div id="tools">
			<ul>
				<li><a href='javascript:void(0);' onclick='window.open("<?php echo ROOT_URI?>admin/filemanager/", "Файловый менеджер", "width=500,height=500");' title="Открыть файловый менеджер"> Файловый менеджер</a></li>
				<li><a href="<?php echo ROOT_URI;?>" target="_blank" class="icon-home" title="Открыть сайт"></a></li>
				<li><a href="<?php echo ROOT_URI;?>user/exit/" class="icon-logout" title="Выйти"></a></li>
			</ul>
		</div>
		<div>
			<div id="icon-menu" class="icon-menu" title="Свернуть/Развернуть меню"></div>
			<div id="logo"><img src="<?php echo ADMIN_THEME;?>img/jump.png"></div>
		</div>
	</header>
	<?php 
		include 'dashboard.php';
	?>
	<div id="content">