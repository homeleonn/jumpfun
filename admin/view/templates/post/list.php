<?php 
use Jump\helpers\Common;
?>
<a href="<?=SITE_URL?>admin/<?=Common::postSlug()?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить</span></a>
<div style="overflow-x: auto;">
	<table class="mytable">
		<tr align="center">
			<td>title/url</td><td>Дата публикации</td><td></td><td></td>
		</tr>
		<?php
		$slug = Common::postSlug();
		foreach($data as $page):
			$link = '<a target="_blank" href="' . ROOT_URI . $slug . '/' . $page['url'] . '/">' . $page['title'] . '</a>';
		?>
		<tr>
				<td><?=$link;?></td>
				<td><?=$page['created'];?></td>
				<td>
					<a title="<?=$options['edit']?>"  target="blank" href="<?=SITE_URL?>admin/<?=$options['slug']?>/edit/<?=$page['id'];?>/">
						<span class="icon-pencil block"></span>
					</a>
				</td>
				<td>
					<a href="javascript:void(0);" title="<?=$options['delete']?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$options['slug']?>',<?=$page['id'];?> );">
						<span class="icon-cancel red block"></span>
					</a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>