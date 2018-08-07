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
				<div class="container main-title">
					<h1 class="center">Организация детских праздников <br><span class="upper">funkids</span> Одесса</h1>
				</div>
				<section class="s-about">
					<div class="container">
						<div class="row">
							<div class="col-md-4">
								<h3>Дети хотят веселья!</h3>
								<div class="floatimg"><img src="<?=THEME?>img/about1.jpg" alt=""></div>
								<p>Если Ваш ребёнок уже в том прекрасном возрасте, когда пришло время устраивать громкий праздник - FUNKIDS тут как тут! Мы организуем программу с самыми интересными и веселыми героями.</p>
							</div>
							<div class="col-md-4">
								<h3>Мы с детьми на "ты"</h3>
								<div class="floatimg"><img src="<?=THEME?>img/about2.jpg" alt=""></div>
								<p>Наши аниматоры всегда находят подход ко всем детям, Заказывая программу FUNKIDS можете быть уверенны, что ребёнок запомнит этот праздник надолго и будет рассказывать своим друзьям.</p>
							</div>
							<div class="col-md-4">
								<h3>Оплачивайте как Вам удобно</h3>
								<div class="floatimg"><img src="<?=THEME?>img/about3.jpg" alt=""></div>
								<p>Мы доверяем своим клиентам, поэтому оплата возможна в момент выступления. Также мы принимаем оплату на карту ПриватБанка.</p>
							</div>
						</div>
						
						<h3>Почему выбирают именно нас?</h3>
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, est, dolore totam nobis cupiditate repellendus modi iure nisi natus quo dicta quibusdam omnis ullam aspernatur illum! Qui, assumenda, sequi. <br>Voluptates.
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nulla, est, dolore totam nobis cupiditate repellendus modi iure nisi natus quo dicta quibusdam omnis ullam aspernatur illum! Qui, assumenda, sequi. Voluptates.
						<div class="col-md-6">
							<ul class="my">
								<li>natus quo dicta quibusdam omnis</li>
								<li>natus quo dicta quibusdam omnis</li>
								<li>natus quo dicta quibusdam omnis</li>
							</ul>
						</div>
						<div class="col-md-6">
							<ul class="my">
								<li>natus quo dicta quibusdam omnis</li>
								<li>natus quo dicta quibusdam omnis</li>
								<li>natus quo dicta quibusdam omnis</li>
							</ul>
						</div>
					</div>
				</section>
				<div class="girl-left"><img src="<?=THEME?>img/girl2.png" alt=""></div>
	<?php
	funKids_popular();
	?>
	</div>
	<?php
	funkids_getLastReviews();
	?>
	<div class="container center">
		<a href="<?=uri('reviews')?>" class="button">Перейти ко всем отзывам</a>
		<a href="#" class="button get-review-form">Оставить отзыв</a>
	</div>

	<?php
	echo Common::setCache($cacheFileName);
}else{
	echo 'Не найдено';
}