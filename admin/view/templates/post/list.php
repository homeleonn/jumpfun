<?php 
use Jump\helpers\Common;
//var_dump($data);exit;
?>
<a href="<?=SITE_URL?>admin/<?=$options['type']?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить</span></a>
<div style="overflow-x: auto;">
	<!--<table class="mytable">
		<tr align="center">
			<td>title/url</td><td>Дата публикации</td>
		</tr>
		<?php
		
		// foreach($data as $page):
			// $link = '<a target="_blank" href="' . ROOT_URI . (Common::isPage() ? '' : $options['rewrite']['slug'] . '/') . $page['url'] . '/">' . $page['title'] . '</a>';
		/* 
		<tr>
			<td><?=$link;?></td>
			<td><?=$page['created'];?></td>
			<td>
				<a title="<?=$options['edit']?>"  target="blank" href="<?=SITE_URL?>admin/<?=$options['rewrite']['slug']?>/edit/<?=$page['id'];?>/">
					<span class="icon-pencil block"></span>
				</a>
			</td>
			<td>
				<a href="javascript:void(0);" title="<?=$options['delete']?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$options['rewrite']['slug']?>',<?=$page['id'];?> );">
					<span class="icon-cancel red block"></span>
				</a>
			</td>
		</tr> */
		//endforeach; 
		?>
	</table>-->
	<?=$data;?>
</div>