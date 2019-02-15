<?//=dd(get_defined_vars());?>
<div class="list-wrapper container">
	<?=isset($post['h1'])?'<h1>'.$post['h1'].'</h1>':''?>
	<div class="col-sm-12 flex line">
		<?php 
		if($this->haveChild()):
			while($item = $this->theChild()):
		?>
		<div class="col-sm-4 list-item center">
			<div>
				<a href="<?=$item['url']?>">	
					<img src="<?=(isset($item['_jmp_post_img']) ? UPLOADS . $item['_jmp_post_img'] : THEME . 'img/logo_trnsprnt1.png')?>" alt="">
					<div class="itemcontent">
						<div class="inline-title"><?=$item['title']?></div>
					</div>
				</a>
			</div>
		</div>
		<?php 
			endwhile;
			doAction('after_show_list', $post);
		else:
			echo 'Архивов нет!';
		endif;
		?>
		
	</div>
</div>
<?php if(isset($rewrite['paged']) && $rewrite['paged']): ?>
<?=$pagenation;?>
<?php endif; ?>