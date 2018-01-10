<?//=var_dump(get_defined_vars());?>
<div class="list-wrapper container-fluid">
	<div class="col-sm-9" style="float: right;">
		<?php 
		if($this->haveChild()):
			while($educator = $this->theChild()):
		?>
		<div class="col-sm-3 list-item">
			<div>
				<a href="<?=SITE_URL . $rewrite['slug'] . '/' . $educator['url']?>/">
					<div class="thumb"><img src="<?=THEME . 'img/news_thumb.jpg'?>" alt="" width="100%"></div>
					<div class="name"><?=$educator['title']?></div>
				</a>
			</div>
		</div>
		<?php 
			endwhile;
		else:
			echo 'Педагогов нет!';
		endif;
		?>
		
	</div>
	<div class="col-sm-3">
		<?=$filters;?>
	</div>
	
</div>
<?=$pagenation;?>


<!--
<div class="row">
	<div style="border: 2px lightblue solid; border-radius: 10px; padding: 10px; margin: 10px;">
	<?php //if($styles = $this->senderModel->getTermsListByTaxonomy('style')):?>
		Стили: <?//=implode(' | ', $styles) . '<hr>'?>
	<?php //endif;?>
	
	<?//=$this->senderModel->getTermsListByTaxonomy('age', ' || ');?>
	</div>
</div>-->