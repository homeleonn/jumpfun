<?php  
//var_dump(admin\AdminController::$postType);
//var_dump($data);
?>
<h2><?=$options['edit']?></h2>
<form method="POST" id="edit-<?=admin\AdminController::$postType?>" class="post-from-admin" name="" autocomplete="off">
	<div id="center">
		<input type="hidden" name="id" value="<?=$data['id']?>">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" value="<?=$data['title']?>" type="text" name="title" id="title" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>URL</div>
			<div><input class="w100" value="<?=$data['url']?>" type="text" name="url" id="url" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Текст</div>
			<div id="editors"><textarea name="content" id="content" value="1" style="width: 100%;height: 600px;display: none; visibility:hidden;"><?=htmlspecialchars($data['content'])?></textarea></div>
		</div>
	</div>
	<div id="sidebar-right">
		<br><br>Добавлено: <?=$data['created']?>
		<br>Последнее редактирование: <?=$data['modified']?>
		<br><br><input type="button" id="item-factory" value="Редактировать">
		
		<?php include $this->getFile('sidebarRight');?>
	</div>
	
</FORM>