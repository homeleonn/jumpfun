<?php if($comment_status == 'open'):?>
<div id="post-comments" class="side-block">
	<div class="block-title">Комментарии</div>
	<div class="block-content clearfix" style="width: 70%; margin: 0 auto;">
		<?php
		if($comments): 
			$commentCount = count($comments);
			foreach($comments as $comment):
		?>
		<table border="1" width="100%" style="width: 100%;">
			<tr>
				<td><?=$comment['comment_author']?></td>
				<td width="100%"><?=$comment['comment_date']?></td>
				<td><span class="icon-comment"></span></td>
				<td>№<?=$commentCount--?></td>
			</tr>
			<tr>
				<td colspan="5"><?=$comment['comment_content']?></td>
			</tr>
		</table>
		<?php endforeach;endif; ?>
		<input type="button" value="Добавить комментарий" id="admin-comment-block-show" class="btn">
		<div id="admin-comments-block" class="none">
			<textarea id="comment-text" cols="30" rows="10" style="width: 100%; height: 150px;"></textarea>
			<input type="button" value="Добавить" id="admin-add-comment" class="btn" style="margin-top: 5px;">
			<input type="button" value="Отмена" id="admin-cancel-comment" class="btn2" style="float: right; margin-top: 5px;">
		</div>
	</div>
</div>
<?php endif;?>