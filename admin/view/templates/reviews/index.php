<h1>Отзывы клиентов</h1>
<table class="mytable">
	<tr align="">
		<td width="20%">Имя</td>
		<td>Текст</td>
		<td width="1%">Статус</td>
		<td width="1%">Дата добавления</td>
		<td width="1%"></td>
	</tr>
	<?php foreach($data['reviews'] as $review): ?>
		<tr>
			<td><?=$review['name']?></td>
			<td><?=$review['text']?></td>
			<td><a href="<?=uri('review/toggle/' . $review['id'])?>"><span class="<?=$review['status']?'green icon-ok-circled':'red icon-cancel-circled'?>"></span></a></td>
			<td><?=$review['created']?></td>
			<td><a href="<?=uri('review/delete/' . $review['id'])?>"><span class="red icon-cancel" title="Удалить отзыв"></span></a></td>
		</tr>
	<?php endforeach; ?>
</table>
