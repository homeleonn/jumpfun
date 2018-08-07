<?//=dd(get_defined_vars());?>
<div class="list-wrapper container">
	<?=isset($post['h1'])?'<h1>'.$post['h1'].'</h1>':''?>
	<div class="col-sm-12 flex line">
		<?php 
		if($this->haveChild()):
			while($post = $this->theChild()):
		?>
		<div class="col-sm-4 list-item center">
			<div>
				<a href="<?=$post['url']?>">	
					<img src="<?=(isset($post['_jmp_post_img']) ? UPLOADS . $post['_jmp_post_img'] : THEME . 'img/002.jpg')?>" alt="">
					<div class="itemcontent">
						<div class="inline-title"><?=$post['title']?></div>
					</div>
				</a>
			</div>
		</div>
		<?php 
			endwhile;
		else:
			echo 'Архивов нет!';
		endif;
		?>
		
	</div>
</div>
<?php if(isset($rewrite['paged']) && $rewrite['paged']): ?>
<?=$pagenation;?>
<?php endif; ?>