<?php
//var_dump(get_defined_vars());
?>
<div class="col-sm-9" style="float: right;">
	<?php echo $selectedFilters;?>
	<div class="row">
	<?php foreach($products as $product):?>
		
		<a href="<?=SITE_URL . $product['url'] . '-p' . $product['id'] . '/'?>">
			<div class="col-sm-3">
				<div style="float: left; padding: 10px;">
					<img src="<?=THEME . 'img/news_thumb.jpg'?>">
				</div>
				<div style="padding: 10px;"><?=$product['title']?></div>
			</div>
		</a>
	<?php endforeach;?>
	</div>
</div>

<div class="col-sm-3">
	<?php echo $filters;?>
</div>
<div class="sep"></div>