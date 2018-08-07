	</div>
	<footer class="<?=URI=='/'?'index':''?>">
			<div class="plain"></div>
			<div class="container-fluid">
				<div class="container">
					<div class="row top">
						<div class="col-md-<?=isMain()?3:6?>">
							<div class="logo-text">
								<a href="#" class="clearfix">
									<div class="first-word">Fun</div>
									<div class="second-word">Kids</div>
								</a>
								<div class="inline-title center">Огранизация детских праздников</div>
							</div>
						</div>
						<?php if(isMain()):?>
						<div class="col-md-6">
							<div class="carousel-widget mini-gallery" data-carousel-widget-column="1">
								<div class="widget-head">
									<div class="title">Мини-галерея</div>
									<div class="controls">
										<div class="rightside"></div>
										<div class="leftside"></div>
									</div>
								</div>
								<div class="widget-content">
									<div class="inside-content shower">
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/1.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/22.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/33.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/44.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/5.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/7.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/8.jpg" alt="" /></div></div>
								<div class="item"><div class="img"><img src="<?=THEME?>img/mini-gallery/10.jpg" alt="" /></div></div>
									</div>
								</div>
							</div>
						</div>
						<?php endif;?>
						<div class="col-md-<?=isMain()?3:6?> center">
							<div class="fs25 mb30">
								Позвоните нам:<br>
								(067) 797-93-85 <br>
								(063) 200-85-95
							</div>
							
							
							<div class="fs25">Наш офис:</div>
							Одесса, ул. Дерибасовская, 5
						</div>
					</div>
					
					<div class="copyright">Copyrights © 2018: Fun Kids Odessa</div>
					<div class="social-bottom">
						<a href="#" rel="nofollow"><span class="facebook icon-facebook-1"></span></a>
						<a href="#" rel="nofollow"><span class="twitter icon-twitter"></span></a>
						<a href="#" rel="nofollow"><span class="youtube icon-youtube"></span></a>
						<a href="#" rel="nofollow"><span class="gplus icon-gplus"></span></a>
					</div>
				</div>
			</div>
		</footer>
		<div class="up"></div>
	</div>
	<div id="shower">
		<span></span>
		<div id="img">
			<img src="" alt="Просмотр изображения">
			<div id="showerTools">
				<div></div>
				<div></div>
			</div>
			<div id="counter">0 / 0</div>
			<div id="close">x</div>
		</div>
	</div>
	<div id="note-wrap">
		<span style="width: 100%;height:100%; position: absolute; z-index: 1; display: block;" onclick="note.hide()"></span>
		<div id="note" style="z-index: 2;">
			<div id="note-title">Заголовок</div>
			<div id="note-content">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
			<div style="text-align: center;">
				<button id="note-submit" class="button7">Отправить</button>
				<button id="note-close" onclick="note.hide()">X</button>
			</div>
		</div>
	</div>
	
	<div id="order-question">
		<form>
			<div class="inp">
				<input type="text" id="qname" placeholder="Ваше имя">
				<input type="text" id="qtel"  placeholder="Ваш телефон">
				<input type="text" id="qmail" name="email" placeholder="Ваша электронная почта*" onblur="checkBlurMail(this)">
			</div>
			<textarea id="qq" placeholder="Введите Ваше сообщение"></textarea>
			<input type="button" class="button7" id="q-set" value="Отправить">
		</form>
	</div>
	
	<div id="callme">
		<input type="text" id="call-tel"  placeholder="Ваш телефон">
		<input type="button" id="call-set" value="Отправить">
	</div>
	
	<div id="review-form">
		<input type="text" id="review-name"  placeholder="Ваше имя">
		<textarea id="review-text" placeholder="Введите Ваш отзыв" class="limit-symbols" data-limit="500" rows="5"></textarea>
		<div id="captcha-wrapper" style="margin: 10px 0;">
			<img alt="captcha" id="captcha" class="pointer captcha-reload">
			<span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span> <br>Введите символы с картинки 
			<input type="text" id="captcha-code" name="captcha_code">
		</div>
		<input type="button" id="review-set" class="review-set" value="Отправить">
	</div>
	<script>
		var root = <?=ROOT_URI?>;
	</script>
</body>
</html>