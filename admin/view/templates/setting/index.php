<?php
//dd($data);
	$mainPageDataLast = $data['settings']['front_page'] == 'last';
	
?>
<form action="<?=uri('settings/save')?>" method="POST">
	На главной странице отображать:<br>
	<label><input type="radio" name="front_page" value="last" <?=($mainPageDataLast ? 'checked' : '')?> style="margin: 0 3px 0 20px;"> Последние записи</label><br>
	<label><input type="radio" name="front_page" value="static" <?=(!$mainPageDataLast ? 'checked' : '')?> style="margin: 0 3px 0 20px;"> Статическую страницу</label><br>
	<div style="width: 300px;"><?=$data['settings']['listForParents']?></div><br>
	<input type="submit" value="Сохранить">
</form>