<div class="container">
	<section class="reviews list topoffset">
		<h1>Отзывы наших клиентов</h1>
		<?php foreach($reviews as $review): ?>
			<div class="item">
				<div class="floatimg">
					<img src="<?=THEME?>img/review.jpg" alt="Отзыв клиента <?=$review['name']?>" />
				</div>
				<p class="quote-big">
					<?=$review['text']?>
				</p>
				<div class="right fs22"><?=$review['name']?></div>
				<div class="clearfix"></div>
			</div>
		<?php endforeach; ?>
	</section>
	
	<div class="center"><a href="#" class="button">Оставить отзыв</a></div>
</div>
