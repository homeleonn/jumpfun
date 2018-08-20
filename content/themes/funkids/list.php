<?//=dd(get_defined_vars());?>
<div class="list-wrapper container">
	<?php 
		echo isset($post['h1'])?'<h1>'.$post['h1'].'</h1>':'';
		if($post['type'] == 'program'):
	?>
	<div class="col-sm-12 flex line ">
		<?php 
		if($this->haveChild()):
			while($item = $this->theChild()):
		?>
		<div class="col-sm-4 list-item center">
			<div>
				<a href="<?=$item['url']?>">	
					<img src="<?=(isset($item['_jmp_post_img']) ? UPLOADS . $item['_jmp_post_img'] : THEME . 'img/002.jpg')?>" alt="<?=$item['title']?> - шоу программа">
					<div class="itemcontent">
						<div class="inline-title"><?=$item['short_title']?:$item['title']?></div>
					</div>
				</a>
			</div>
		</div>
		<?php 
			endwhile;
			else:
				echo 'Архивов нет!';
			endif;
			elseif($post['type'] == 'service'):
			?><div class="col-sm-12 flex extra-services shower">
		<?php 
		if($this->haveChild()):
			while($item = $this->theChild()):
		?>
		
		<div class="col-md-6 list-item center">
			<div>
				<div class="img"><img src="<?=(isset($item['_jmp_post_img']) ? UPLOADS . $item['_jmp_post_img'] : THEME . 'img/002.jpg')?>" alt="<?=$item['short_title']?:$item['title']?> - дополнительная услуга к детскому празднику на день рождения"></div>
				<a href="<?=$item['url']?>">		
					<div class="itemcontent">
						<div class="inline-title"><?=$item['short_title']?:$item['title']?></div>
					</div>
				</a>
			</div>
		</div>
		<?php 
			endwhile;
			else:
				echo 'Архивов нет!';
			endif;
			else:
			?><div class="col-sm-12 news"><?php
			if($this->haveChild()):
			while($item = $this->theChild()):
			//dd($item);
		?>
			<div class="item shower clearfix">
				<div class="title">
					<a href="<?=$item['url']?>">
						<span class="inline-title"><?=$item['short_title']?:$item['title']?></span>
					</a>
				</div>
				<div class="ncontent">
					<img src="<?=isset($item['_jmp_post_img']) ? funKidsUploadedImgPath($item['_jmp_post_img']) : THEME . 'img/002.jpg'?>" alt="<?=$item['title']?>" style="width: 200px;" class="floatimg">
				
					<span><?=$item['content']?></span>
					<br>
					<div class="right"><?=substr($item['created'], 0, -9)?></div>
				</div>
			</div>
		<?php
			endwhile;
			else:
				echo 'Архивов нет!';
			endif;
			endif;
			doAction('after_show_list', $post);
		?>
		
	</div>
</div>
<?php if(isset($rewrite['paged']) && $rewrite['paged']): ?>
<?=$pagenation;?>
<?php endif; ?>