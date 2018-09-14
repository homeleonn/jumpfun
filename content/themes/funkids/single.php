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
	<?php 
		if(isAdmin()){
			echo '<a href="'.SITE_URL.'admin/'.$post_type.'/edit/'.$id.'/" title="Редактировать"><span class="icon-pencil"></span></a>';
		}
	?>
</div>