<?//=dd(get_defined_vars());?>
<div class="container">
	<?=isset($h1)?'<h1>'.$h1.'</h1>':''?>
	<div class="floatimg main-img">
		<a href="<?=postImgSrc($post)?>" class="shower">
			<img src="<?=postImgSrc($post, 'medium')?>" alt="<?=$h1??''?>">
		</a>
	</div>
	<?php if(isset($terms)) echo $terms;?>
	<?=applyFilter('single_before_content', $post)?>
	<div  class="tcontent"><?=$content?></div>
	<?php //include $this->get('comments');?>
	<?php if($post_type == 'service') funkids_ilike($id, $post_type, getPageOptionsByType($post_type)['title']);?>
</div>