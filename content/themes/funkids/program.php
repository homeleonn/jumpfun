<?//=dd(get_defined_vars())?>
<div class="container program">
	<noindex>
	<div class="col-md-3 col-xs-0 heroes-catalog-wrapper">
		<div class="heroes-catalog">
			<div class="ribbon"><div class="title center ribbon-content">КАТАЛОГ ГЕРОЕВ</div></div>
			<div class="list">
				<?=funKids_catalogHeroes()?>
			</div>
		</div>
	</div>
	</noindex>
	<div class="col-md-9 shower">
		<?=isset($h1)?'<h1>'.$h1.'</h1>':''?>
		<div class="floatimg main-img">
			<a href="<?=postImgSrc($post)?>" title="<?=$short_title?>" class="shower">
				<img src="<?=postImgSrc($post, 'medium')?>" data-large-img="<?=postImgSrc($post)?>" alt="<?=$h1?>">
			</a>
		</div>
		<?php if(isset($terms)) echo $terms;?>
		<div class="tcontent">
			<div class="price">0 грн/час</div><br>
			<?=$content?>
		</div>
	</div>
	<noindex>
	<div class="sep"></div>
	<div class="container">
		<div class="inline-title">В программу входят</div>
		<div class="row center prog-filling flex line">
			<div><img src="<?=THEME?>img/costumes.jpg" alt="Костюмы аниматоров"><div class="inline-title">Костюмы</div></div>
			<div><img src="<?=THEME?>img/interactive.jpg" alt="Детская интерактивная программа Одесса"><div class="inline-title">Интерактивная программа</div></div>
			<div><img src="<?=THEME?>img/props.jpg" alt="Реквизит на шоу программу, праздник"><div class="inline-title">Реквизит</div></div>
			<div><img src="<?=THEME?>img/musical-equipment.jpg" alt="Музыка, музыкальный реквизит для детских аниматоров в Одессе"><div class="inline-title">Музыкальная аппаратура</div></div>
			<div><img src="<?=THEME?>img/dj.jpg" alt="Диджей, DJ, Ди-джей, музыка на день рождения ребенка"><div class="inline-title">Диджей</div></div>
		</div>
		<div class="sep"></div>
		<div class="inline-title small center">Не забывайте о наших <a class="under" href="<?=uri('services')?>">дополнительных услугах</a>, что бы сделать праздник еще ярче! <br><a href="<?=uri('services')?>" class="button">Перейти к доп. услугам</a></div>
		
	</div>
	
	<div class="sep"></div>
	
	<?php //include $this->get('comments');?>
	<?php funKids_like($id)?>
	</noindex>
</div>