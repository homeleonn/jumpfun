<?//=var_dump(get_defined_vars());exit;?>
<div class="list-wrapper container-fluid">
	<div class="col-sm-<?php if(empty($filters)): ?>12<?php else: ?>9<?php endif; ?>" style="float: right;">
		<?php 
		if($this->haveChild()):
			while($post = $this->theChild()):
		?>
		<div class="col-sm-3 list-item">
			<div>
				<a href="<?=$post['url']?>">
					<div class="thumb"><img src="<?=THEME . 'img/news_thumb.jpg'?>" alt="" width="100%"></div>
					<div class="name"><?=$post['title']?></div>
				</a>
				<?php if($post['terms']): ?>
				<div><ul><?=$post['terms']?></ul></div>
				<?php endif; ?>
			</div>
		</div>
		<?php 
			endwhile;
		else:
			echo 'Архивов нет!';
		endif;
		?>
		
	</div>
	<?php if(!empty($filters)): ?>
	<div class="col-sm-3">
		<?=$filters;?>
	</div>
	<?php endif; ?>
</div>
<?php if(!isset($rewrite['paged']) && $rewrite['paged']): ?>
<?=$pagenation;?>
<?php endif; ?>