<?//=dd(get_defined_vars())?>
<div class="container program">
	<div class="col-md-3 col-xs-0 heroes-catalog-wrapper">
		<div class="heroes-catalog">
			<div class="ribbon"><div class="title center ribbon-content">КАТАЛОГ ГЕРОЕВ</div></div>
			<div class="list">
				<?=funKids_catalogHeroes()?>
			</div>
		</div>
	</div>
	<div class="col-md-9">
		<?=isset($h1)?'<h1>'.$h1.'</h1>':''?>
		<div class="floatimg shower main-img"><div class="img"><img src="<?=(isset($_jmp_post_img) ? $_jmp_post_img : THEME . 'img/002.jpg')?>" alt="<?=$h1?>"></div></div>
		<?php if(isset($terms)) echo $terms;?>
		<div class="tcontent"><?=$content?></div>
	</div>
	<div class="sep"></div>
	<div class="container shower">
		<div class="inline-title">В программу входят</div>
		<div class="row center prog-filling flex line">
			<div><img src="<?=THEME?>img/costumes.jpg" alt="Костюмы аниматоров"><div class="inline-title">Костюмы</div></div>
			<div><img src="<?=THEME?>img/interactive.jpg" alt="Детская интерактивная программа Одесса"><div class="inline-title">Интерактивная программа</div></div>
			<div><img src="<?=THEME?>img/props.jpg" alt="Реквизит на шоу программу, праздник"><div class="inline-title">Реквизит</div></div>
			<div><img src="<?=THEME?>img/musical-equipment.jpg" alt="Музыка, музыкальный реквизит для детских аниматоров в Одессе"><div class="inline-title">Музыкальная аппаратура</div></div>
			<div><img src="<?=THEME?>img/dj.jpg" alt="Диджей, DJ, Ди-джей, музыка на день рождения ребенка"><div class="inline-title">Диджей</div></div>
		</div>
		<div class="sep"></div>
		<div class="inline-title center">Не забывайте о наших <a href="<?=uri('services')?>">дополнительных услугах</a>, что бы сделать праздник еще ярче! <br><a href="<?=uri('services')?>" class="button">Перейти к доп. услугам</a></div>
	</div>
	
	<div class="sep"></div>
	
	<?php //include $this->get('comments');?>
	<?php 
		if(isAdmin()){
			echo '<a href="'.SITE_URL.'admin/'.$post_type.'/edit/'.$id.'/" title="Редактировать"><span class="icon-pencil"></span></a>';
		}
	?>
	<?php funKids_like($id)?>
</div>