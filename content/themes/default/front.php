<?//=var_dump(get_defined_vars());exit;?>
<div class=" container">
	<div class="col-sm-12">
		<?php 
		if($this->haveChild()):
			while($post = $this->theChild()):
			//dd($post);
			?>
			<div class="col-sm-12 front-list" style="margin-bottom: 50px; background: #c8c8c8;">
				<div>
					<a href="<?=$post['url']?>">
						<div class="name" style="font-size: 30px; margin: 5px 5px 10px; color: black;font-weight: bold;">
							<?=$post['title']?>
						</div>
					</a>
					<div class="thumb" style="text-align: center;">
						<img style="max-height: 400px; display: inline;" class="shower" src="<?=isset($post['_jmp_post_img']) ? UPLOADS . $post['_jmp_post_img'] : THEME . 'img/news_thumb.jpg'?>">
					</div>
					<div style="background: #9b9b9b;padding: 5px;margin: 10px;border-radius: 20px; font-weight: bold;" class="post-meta">
						<span class="icon-calendar"></span> <a href="<?=$post['url']?>"><?=substr($post['created'], 0, -3)?></a>
						<?php if($post['terms']): ?>
						 | <span class="icon-folder"></span> <ul><?=$post['terms']?></ul>
						<?php endif; ?>
						
						 | <span class="icon-comment"></span><a href="<?=$post['url']?>#post-comments">
						<?=$post['comment_count'] ? "Комментарии({$post['comment_count']})" : 'Добавить комменатрий';?>
						</a>
						
						<?php if(isAdmin()): ?>
						 | <span class="icon-pencil"></span><a href="<?=uri('admin/post/edit/' . $post['id'])?>">Изменить</a>
						<?php endif; ?>
					</div>
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