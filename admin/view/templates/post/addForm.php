<?php  
//var_dump(admin\AdminController::$postType);
//var_dump(get_defined_vars());exit;
//var_dump($options, $data);exit;
?>
<h2><?=$options['add']?></h2>
<form method="POST" id="add-<?=$options['type']?>" class="post-from-admin" name="" autocomplete="off">
	<div id="center">
		<input type="hidden" name="id">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" type="text" name="title" id="title" placeholder=""></div>
		</div>
		<div class="block1">
			<div>Текст</div>
			<div id="editors"><textarea class="visual" name="content" id="content" value="1" style="width: 100%;height: 600px;display: none; visibility:hidden;"></textarea></div>
		</div>
	</div>
	
	<!-- Block for add post categories -->
	<!-- Block for add post tags -->
	<div id="sidebar-right">
		<input type="button" id="item-factory" value="Добавить">
		<input type="submit" id="" value="Добавить">

		<?php include $this->getFile('sidebar/categoriesAndTags');?>
		<?php include $this->getFile('sidebar/listForParents');?>
	</div>
	
	<div class="sep"></div>
</form>