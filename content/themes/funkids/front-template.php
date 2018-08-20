<?php
use Jump\helpers\Common;
/**
 *  Template: front
 */
 
if(isset($content)){
	//echo $content;
	$cacheFileName = 'front/index';
	//if(Common::getCache($cacheFileName, -1)) return;
	
	?>
	<div class="popular">
		<div class="header-img">
			<h1 class="center">Организация детских праздников в Одессе: аниматоры, шоу программы.
			<div class="logo-text">
				<span class="upper"><div class="first-word">FunKids</div></span>
			</div>
			</h1>
		</div>
		<?=funKids_all();?>
	<?php
	//funKids_popular();
	?>
	</div>
	<?php
	funKids_services();
	?>
	<div class="s-about" id="s-about">
		<div class="girl-left"><img src="<?=THEME?>img/girl2.png" alt="Веселая девочка на празднике"></div>
		<h2 class="section-title">О нас</h2>
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<h3>Дети хотят веселья!</h3>
					<div class="floatimg"><img src="<?=THEME?>img/about1.jpg" alt="Дети хотят веселья! Организация праздников Одесса"></div>
					<p>Если Ваш ребёнок уже в том прекрасном возрасте, когда пришло время устраивать громкий праздник - FUNKIDS тут как тут! Мы организуем программу с самыми интересными и веселыми героями.</p>
				</div>
				<div class="col-md-4">
					<h3>Мы с детьми на "ты"</h3>
					<div class="floatimg"><img src="<?=THEME?>img/about2.jpg" alt="Дети на празднике Одесса, разноцветные шарики"></div>
					<p>Наши аниматоры всегда находят подход ко всем детям, Заказывая программу FUNKIDS можете быть уверенны, что ребёнок запомнит этот праздник надолго и будет рассказывать своим друзьям.</p>
				</div>
				<div class="col-md-4">
					<h3>Оплачивайте как Вам удобно</h3>
					<div class="floatimg"><img src="<?=THEME?>img/about3.jpg" alt="Удобная оплата детского праздника"></div>
					<p>Мы доверяем своим клиентам, поэтому оплата возможна в момент выступления. Также мы принимаем оплату на карту ПриватБанка.</p>
				</div>
			</div>
			
			<h3>Почему выбирают именно нас?</h3>
			<strong>Организаторы детских праздников FunKids Одесса</strong> - это профессионалы своего дела - <strong>аниматоры</strong>, которые вкладываю большой труд не только на выступлениях, но и в постоянной работе над самими собой. <strong>Яркие костюмы</strong> и реквизиты, интересные <strong>шоу программы</strong>, улыбки на лицах ваших детей, не забываемые впечатления, а самое главное это большое удовольствие, радость и неизгладимые впечатление, которые мы приносим деткам и их родителям. Мы всегда готовы помочь в выборе заведения для проведения вашего праздника. Работаем по всей <strong>Одессе</strong> и за её приделами. У нас есть огромный выбор всевозможных развлекательных программ в которых участвуют не только дети, но и взрослые. В каждой программе есть свой тематический реквизит, она насыщенна развлекательными и танцевальными конкурсами. Изюминка наших аниматоров - хорошие танцоры и акробаты.
			
			<h3>В детскую шоу программу входят</h3>
			<div class="col-md-6">
				<ul class="my">
					<li>Костюмы</li>
					<li>Интерактивная программа</li>
					<li>Реквизит</li>
				</ul>
			</div>
			<div class="col-md-6">
				<ul class="my">
					<li>Музыкальная аппаратура</li>
					<li>Диджей</li>
				</ul>
			</div>
		</div>
	</div>
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
	funkids_getLastReviews();
	?>
	
	<?php
	echo Common::setCache($cacheFileName);
}else{
	echo 'Не найдено';
}