<?php 
use Jump\helpers\Common;
$slug = 'categories';
?>
<a href="<?=SITE_URL?>admin/<?=$slug?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить</span></a>
<div style="overflow-x: auto;">
	<table class="mytable">
		<tr align="center">
			<td>title/url</td><td>Дата публикации</td><td></td><td></td>
		</tr>
		<?php
		foreach($data as $page):
			$link = '<a target="_blank" href="' . ROOT_URI . $page['url'] . '-c'.$page['id'].'/">' . $page['title'] . '</a>';
		?>
		<tr>
				<td><?=$link;?></td>
				<td><??></td>
				<td>
					<a title=""  target="blank" href="<?=SITE_URL?>admin/<?=$slug?>/edit/<?=$page['id'];?>/">
						<span class="icon-pencil block"></span>
					</a>
				</td>
				<td>
					<a href="javascript:void(0);" title="" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$slug?>',<?=$page['id'];?> );">
						<span class="icon-cancel red block"></span>
					</a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>