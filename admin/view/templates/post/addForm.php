<?php  
//var_dump(admin\AdminController::$postType);
//var_dump(get_defined_vars());exit;
//var_dump($options, $data);exit;
?>
<h2><?=$options['add']?></h2>
<form method="POST" id="add-<?=$options['slug']?>" class="post-from-admin" name="" autocomplete="off">
	<div id="center">
		<input type="hidden" name="id">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" type="text" name="title" id="title" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>URL</div>
			<div><input class="w100" type="text" name="url" id="url" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Текст</div>
			<div><textarea name="content" id="content" value="1" style="width: 100%;height: 600px;"></textarea></div>
		</div>
	</div>
	
	<!-- Block for add post categories -->
	<!-- Block for add post tags -->
	<div id="sidebar-right">
		<input type="button" id="item-factory" value="Добавить">
		<input type="submit" id="" value="Добавить">

		<?php include $this->getFile('sidebarRight');?>
	</div>
	
	<div class="sep"></div>
	
</form>