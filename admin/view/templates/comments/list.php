<?php //dd(get_defined_vars());?>
<table class="mytable comment-list">
	<tr align="center">
		<td width="20%">Автор</td>
		<td>Комментарий</td>
		<td width="1%">Запись</td>
		<td width="1%">Дата публикации</td>
	</tr>
	<?php 
		foreach($data['comments'] as $comment):
		if(!isset($data['posts_on_id'][$comment['comment_post_id']])) continue;
		$post = $data['posts_on_id'][$comment['comment_post_id']];
	?>
	<tr class="comment-cell" data-id="<?=$comment['comment_id']?>">
		<td style="font-size: 12px;">
			<div class="avatar"></div>
			<?=$comment['comment_author']?>
			<br>
			<?=$comment['comment_author_email']?>
			<br>
			<?=$comment['comment_author_ip']?>
		</td>
		<td align="left" style="text-align: left;" class="comment-content-cell">
			<div class="content"><?=$comment['comment_content']?></div>
			<div class="options">
				[<span style="color: coral;" class="pointer edit">Изменить</span>] 
				[<span style="color: red;" class="pointer remove">Удалить</span>]
			</div>
		</td>
		<td>
			<a href="<?=SITE_URL?>admin/<?=$post['post_type']?>/edit/<?=$post['id']?>/"><?=$post['title']?></a><br>
			<a href="<?=$post['url']?>" style="font-size: 10px;">просмотреть запись</a>
		</td>
		<td><?=$comment['comment_date']?></td>
	</tr>
	<?php endforeach; ?>
</table>

<form id="comments-block-form-edit" class="none" style="margin-bottom: 20px;">
	<textarea id="comment-text" name="content" cols="30" rows="10" style="width: 100%; height: 150px; color: black;"></textarea>
	<input type="submit" value="Сохранить" class="btn" style="margin-top: 5px;">
</form>