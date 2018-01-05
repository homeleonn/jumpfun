<a href="<?=SITE_URL?>admin/<?=$options['slug']?>/add-term/?term=<?=$data['term']?>" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить</span></a>
<div style="overflow-x: auto;">
	<table class="mytable">
		<tr align="center">
			<td>title/url</td><td>count</td><td></td><td></td>
		</tr>
		<?php
		foreach($data['terms'] as $term):
			$link = '<a target="_blank" href="' . ROOT_URI . $options['slug'] . '/' . $data['term'] . '/' . $term['slug'] . '/">' . $term['name'] . '</a>';
		?>
		<tr>
				<td><?=$link;?></td>
				<td><?=$term['count'];?></td>
				<td>
					<a title="<?=$options['edit']?>"  target="blank" href="<?=SITE_URL?>admin/<?=$options['slug']?>/edit-term/<?=$term['id'];?>/">
						<span class="icon-pencil block"></span>
					</a>
				</td>
				<td>
					<a href="javascript:void(0);" title="Удалить категорию" onclick="if(confirm('Подтвердите удаление')) delItem(this, '<?=$options['slug']?>', <?=$term['id'];?>, '<?=$data['term']?>' );">
						<span class="icon-cancel red block"></span>
					</a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
</div>