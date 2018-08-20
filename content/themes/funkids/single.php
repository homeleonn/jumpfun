<?//=dd(get_defined_vars());?>
<div class="container">
	<?=isset($h1)?'<h1>'.$h1.'</h1>':''?>
	<div class="floatimg shower main-img"><div class="img"><img src="<?=(isset($_jmp_post_img) ? $_jmp_post_img : THEME . 'img/002.jpg')?>" alt=""></div></div>
	<?php if(isset($terms)) echo $terms;?>
	<div  class="tcontent"><?=$content?></div>
	<?php //include $this->get('comments');?>
	<?php 
		if(isAdmin()){
			echo '<a href="'.SITE_URL.'admin/'.$post_type.'/edit/'.$id.'/" title="Редактировать"><span class="icon-pencil"></span></a>';
		}
	?>
</div>