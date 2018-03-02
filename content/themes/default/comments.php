<?php if($comment_status == 'open'):?>
<div id="post-comments" class="side-block">
	<div class="block-title">Комментарии</div>
	<div class="block-content clearfix" style="width: 70%; margin: 0 auto;">
		<?php
		if($comments): 
			$commentCount = count($comments);
			foreach($comments as $comment):
		?>
		<table>
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
		<form id="comments-block-form">
			<input type="hidden" name="post_id" value="<?=$id?>">
			<?php if(isAuthorized()): ?>
			<div id="comments-block">
				<!--Имя<br>
				<input type="text" value="<?=(session('user.name') ?: '')?>" <?=(!isAuthorized() ? 'disabled style="background: gray !important; "': '')?> name="login"><br>-->
				Текст комментария<br>
				<textarea id="comment-text" name="content" cols="30" rows="10" style="width: 100%; height: 150px; color: black;"></textarea>
				<input type="submit" value="Добавить комментарий" class="btn" style="margin-top: 5px;">
			</div>
			<?php else: ?>
			<a href="<?=SITE_URL?>user/login/">Авторизируйтесь</a> что бы иметь возможность оставлять комментарии.
			<?php endif; ?>
		</form>
	</div>
</div>
<?php endif;?>