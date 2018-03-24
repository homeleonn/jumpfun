<?php

//var_dump(get_defined_vars());
//var_dump($data);
?>
<form method="POST" enctype="multipart/form-data">
	<label>
		<span class="icon-plus add-img green s18">
			<input class="none-impt" multiple type="file" accept="image/jpeg,image/png,image/gif" id="upload-img">
		</span>
	</label>
	<span>Максимальный размер файла: <?=ini_get('upload_max_filesize')?>B</span>
</form>
<div class="row" style="margin-left: 10px;">
	<div class="media-thumbs">
	<?php 
	foreach($data['media'] as $media):
		$img = explode('.', $media['src']);
		$img = UPLOADS . $img[0] . '-150x150.' . $img[1];
		$orig = UPLOADS . $media['src'];
	?>
		<div class="media-thumb"><img src="<?=$img;?>" data-original="<?=$orig?>" data-id="<?=$media['id']?>"></div>
	<?php endforeach;?>
	</div>
	<div id="media-original-show" class=" none">
		<img src="" class="shower">
		<div class="padd5 size b"></div>
		<div class="padd5">Путь:<input type="text" class="w100"></div>
		<span class="icon-pencil"></span>
		<span class="icon-cancel media-delete" id="media-delete"></span><br>
		<div class="btn none padd10" id="select-for-post">Выбрать</div>
	</div>
</div>