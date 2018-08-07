<?//=dd(get_defined_vars());?>
<div class="container">
	<?=isset($h1)?'<h1>'.$h1.'</h1>':''?>
	<div class="floatimg shower"><div class="img"><img src="<?=(isset($_jmp_post_img) ? $_jmp_post_img : THEME . 'img/002.jpg')?>" alt="" style="max-width: 300px;"></div></div>
	<?php if(isset($terms)) echo $terms;?>
	<div style="padding: 10px;"><?=$content?></div>
	<?php //include $this->get('comments');?>
	<?php 
		if(isAdmin()){
			echo '<a href="'.SITE_URL.'admin/'.$post_type.'/edit/'.$id.'/" title="Редактировать"><span class="icon-pencil"></span></a>';
		}
	?>
</div>