<?php 
//var_dump(get_defined_vars());exit;
?>
<a href="<?=SITE_URL?>admin/<?=$options['slug']?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить</span></a>
<div style="overflow-x: auto;">
	<table class="mytable">
		<tr align="center">
			<td>title/url</td><td>count</td><td></td><td></td>
		</tr>
		<?php
		foreach($data as $category):
			$link = '<a target="_blank" href="' . ROOT_URI . $options['category_slug'] . '/' . $category['slug'] . '/">' . $category['slug'] . '</a>';
		?>
		<tr>
				<td><?=$link;?></td>
				<td><?=$category['count'];?></td>
				<td>
					<a title="<?=$options['edit']?>"  target="blank" href="<?=SITE_URL?>admin/<?=$options['slug']?>/edit/<?=$category['id'];?>/">
						<span class="icon-pencil block"></span>
					</a>
				</td>
				<td>
					<a href="javascript:void(0);" title="Удалить категорию" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$options['slug']?>',<?=$category['id'];?> );">
						<span class="icon-cancel red block"></span>
					</a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>