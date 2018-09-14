	</div>
	<footer class="<?=URI=='/'?'index':''?>">
			<div class="plain"></div>
			<div class="container-fluid">
				<div class="container">
					<div class="row top">
						<div class="col-md-<?=isMain()?6:6?>">
							<div class="logo-text">
								<a href="<?=SITE_URL?>"><img src="<?=THEME?>img/logo_trnsprnt1.png" alt="Логотип в футере сайта"></a>
								<div class="inline-title center">Организация детских праздников</div>
							</div>
						</div>
						<?php if(isMain()):?>
						<?php endif;?>
						<div class="col-md-<?=isMain()?6:6?> center">
							<div class="fs25 mb30 white">
								Позвоните нам:<br>
								<a href="tel:+380677979385">+38 (067) 797-93-85</a><br>
								<a href="tel:+380632008595">+38 (063) 200-85-95</a>
								<br>Почта: <a href="mailto:funkids@mail" class="fs18">funkidssodessa@gmail.com</a>
							</div>
						</div>
					</div>
					
					<div class="copyright">Copyrights © 2018: Fun Kids Odessa</div>
					<div class="social-bottom">	
						<a href="https://vk.com/club163464318" rel="nofollow" target="_blank"><span class="vk icon-vk"></span></a>
						<a href="https://www.instagram.com/funkids_odessa/" rel="nofollow" target="_blank"><span class="instagram icon-instagram"></span></a>
						<a href="https://www.youtube.com/channel/UChNlyGg0f5F-mDHxy8UP8IQ" rel="nofollow" target="_blank"><span class="youtube icon-youtube"></span></a>
						<a href="https://www.facebook.com/profile.php?id=100004157191483&refsrc=https%3A%2F%2Fm.facebook.com%2Ffbrdr%2F2048%2F100004157191483%2F" rel="nofollow" target="_blank"><span class="facebook icon-facebook-1"></span></a>
					</div>
				</div>
			</div>
		</footer>
		<div class="phone" title="Заказать обратный звонок"><span class="icon-phone"></span></div>
		<div class="up" title="Подняться вверх"></div>
	</div>
	<div id="shower">
		<span></span>
		<div id="img">
			<img src="" alt="Просмотр изображения">
			<!--<div id="showerTools">
				<div></div>
				<div></div>
			</div>-->
			<div id="shower-tools1">
				<div id="shower-prev"></div>
				<div id="shower-next"></div>
			</div>
			<div id="counter">0 / 0</div>
			<div id="shower-title"></div>
			<div id="close">x</div>
		</div>
	</div>
	<div id="note-wrap">
		<span onclick="note.hide()"></span>
		<div id="note">
			<div id="note-title"></div>
			<div id="note-content"></div>
			<div class="center">
				<button id="note-submit" class="button7">Отправить</button>
				<button id="note-close" onclick="note.hide()">X</button>
			</div>
		</div>
	</div>
	
	
	<div id="callme" class="request-wrapper center none">
		<input type="text" class="tel" name="tel" value="" placeholder="Ваш телефон">
		<input type="button" class="send" value="Отправить">
	</div>
	
	<div id="review-form" class="request-wrapper">
		<input type="text" class="name" placeholder="Ваше имя">
		<textarea placeholder="Введите Ваш отзыв" class="text limit-symbols" data-limit="500" rows="5"></textarea>
		<input type="button" class="send" value="Отправить">
	</div>
	
	<div class="captcha-wrapper none" id="captcha-wrapper">
		<img alt="captcha" class="captcha pointer captcha-reload" src="">
		<span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span> <br>Введите символы с картинки 
		<input type="text" class="captcha-code">
	</div>
	
	<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
	<script src="<?=THEME?>js/js.js"></script>
	<script>
		var root = "<?=ROOT_URI?>";
	</script>
	<script>
		$(function(){
			$.getScript("https://www.googletagmanager.com/gtag/js?id=UA-124185796-1", function(){
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', 'UA-124185796-1');
			});
			
		});
		
		
	</script>
	<script type="application/ld+json">
	{
	  "@context" : "http://schema.org",
	  "@type" : "Organization",
	  "name" : "FUNKIDS",
	  "url" : "http://funkids.od.ua/",
	  "sameAs" : [
		"https://vk.com/club163464318","https://www.facebook.com/profile.php?id=100004157191483&refsrc=https%3A%2F%2Fm.facebook.com%2Ffbrdr%2F2048%2F100004157191483%2F",
		"https://www.instagram.com/funkids_odessa/",
		"https://www.youtube.com/channel/UChNlyGg0f5F-mDHxy8UP8IQ"
	  ]
	}
	</script>
</body>
</html>