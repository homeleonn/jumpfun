<div class="container">
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
		<div class="floatimg shower"><div class="img"><img src="<?=(isset($_jmp_post_img) ? $_jmp_post_img : THEME . 'img/002.jpg')?>" alt="" style="max-width: 300px;"></div></div>
		<?php if(isset($terms)) echo $terms;?>
		<div style="padding: 10px;"><?=$content?></div>
		<?php //include $this->get('comments');?>
		<?php 
			if(isAdmin()){
				echo '<a href="'.SITE_URL.'admin/'.$post_type.'/edit/'.$id.'/" title="Редактировать"><span class="icon-pencil"></span></a>';
			}
		?>
	</div>
	<?php funKids_like($id)?>
</div>