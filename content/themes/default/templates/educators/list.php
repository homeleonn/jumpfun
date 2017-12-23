<?php
//var_dump(get_defined_vars());exit;
if(!isset($educators_list) || empty($educators_list)) {
	echo 'Педагогов нет!';
	return;
}

?>

<div class="list-wrapper container-fluid">
	<div class="col-sm-9" style="float: right;">
		<?php foreach($educators_list as $educator):?>

		<div class="col-sm-3 list-item">
			<div>
				<a href="<?=SITE_URL . $slug . '/' . $educator['url']?>/">
					<div class="thumb"><img src="<?=THEME . 'img/news_thumb.jpg'?>" alt="" width="100%"></div>
					<div class="name"><?=$educator['title']?></div>
				</a>
			</div>
		</div>

		<?php endforeach;?>
	</div>
	<div class="col-sm-3">
		<?=$filters?>
	</div>
	
</div>
<?=$pagenation;?>