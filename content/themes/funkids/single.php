<?//=dd(get_defined_vars());?>
<div class="container">
	<?=isset($h1)?'<h1>'.$h1.'</h1>':''?>
	<div class="floatimg main-img">
		<a href="<?=postImgSrc($post)?>" class="shower">
			<img src="<?=postImgSrc($post, 'medium')?>" alt="<?=isset($h1)?$h1:''?>">
		</a>
	</div>
	<?php if(isset($terms)) echo $terms;?>
	<div  class="tcontent"><?=$content?></div>
	<?php //include $this->get('comments');?>
</div>