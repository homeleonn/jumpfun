<?php
if(!$this->haveChild()){
	echo 'Педагогов нет!';
	return;
}	
?>
<div class="list-wrapper container">
	<div class="col-sm-12" style="float: right;">
		
		<?php if($styles = $this->senderModel->getTermsListByTaxonomy('style', $type)):?>
		<div class="row">
			<div style="border: 2px lightblue solid; border-radius: 10px; padding: 10px; margin: 10px;">Стили: <?=implode(' | ', $styles)?></div>
		</div>
		<?php endif;?>
		
		<?php while($educator = $this->theChild()):?>
		<div class="col-sm-3 list-item">
			<div>
				<a href="<?=SITE_URL . $slug . '/' . $educator['url']?>/">
					<div class="thumb"><img src="<?=THEME . 'img/news_thumb.jpg'?>" alt="" width="100%"></div>
					<div class="name"><?=$educator['title']?></div>
				</a>
			</div>
		</div>
		<?php endwhile;?>
		
	</div>
	<!--<div class="col-sm-3">
		<?=$filters;?>
	</div>-->
	
</div>
<?=$pagenation;?>