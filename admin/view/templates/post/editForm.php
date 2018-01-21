<?php  
//var_dump(get_defined_vars());exit;
?>
<?=doAction('admin_post_options_form');?>
<a href="<?=SITE_URL?>admin/<?=$options['type']?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить новую</span></a>
<h2><?=$options['edit']?></h2>
<form method="POST" id="edit-<?=$options['type']?>" class="post-from-admin" name="" autocomplete="off">
	<div id="center">
		<input type="hidden" name="id" value="<?=$data['id']?>">
		<input type="hidden" name="url" value="<?=$data['url']?>">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" value="<?=$data['title']?>" type="text" name="title" id="title" placeholder=""></div>
			<div>
				<a id="url" href="<?=$data['permalink']?>"><span class="anchor"><?=$data['anchor']?></span><span class="editing-part"><?=$data['url']?></span><span id="url-end">/</span></a> 
				<input type="button" id="edit-url-init" value="Изменить" style="padding: 0px 4px;">
				<input type="button" id="edit-url-ok" value="ок" style="padding: 0px 4px; display: none;">
				<input type="button" id="edit-url-cancel" value="отмена" style="padding: 0px 4px; display: none;">
			</div>
		
		</div>
		
		<div class="block1">
			<div>Текст</div>
			<div id="editors"><textarea class="visual" name="content" id="content" value="1" style="width: 100%;height: 600px;display: none; visibility:hidden;"><?=htmlspecialchars($data['content'])?></textarea></div>
		</div>
		
		
		<?php include $this->getFile('sidebar/extraFields');?>
		
	</div>
	<div id="sidebar-right">
		<br><br>Добавлено: <?=$data['created']?>
		<br>Последнее редактирование: <?=$data['modified']?>
		<br><br><input type="button" id="item-factory" value="Редактировать">
		
		<?php include $this->getFile('sidebar/categoriesAndTags');?>
		<?php include $this->getFile('sidebar/listForParents');?>
	</div>
</form>

<?php include $this->getFile('sidebar/extra-field-prototype');?>