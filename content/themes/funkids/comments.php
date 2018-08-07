<?php if($comment_status == 'open'):?>
<div id="post-comments" class="side-block">
	<div class="block-title"><?=__('comments')?> (<span id="comment-count"><?=$comments['count'] ? $comments['count']:0?></span>)</div>
	<div class="block-content clearfix" style="margin: 0 auto;">
		<?php
		if($comments['general']): 
			foreach($comments['general'] as $comment):
				echo themeHTMLCommentTable($comment, isset($comments['sub'][$comment['comment_id']]) ? $comments['sub'][$comment['comment_id']] : NULL, 0);
			endforeach;endif; ?>
		<form id="comments-block-form">
			<input type="hidden" name="post_id" value="<?=$id?>">
			<input type="hidden" name="parent" id="comment-parent" value="0">
			<div id="comments-block">
				<?=__('name')?><br>
				<input type="text" value="<?=(session('user.name') ?: '')?>" <?=(isAuthorized() ? 'disabled style="background: gray !important; "': '')?> name="login" id="comment-login"><br>
				<?=__('comment text')?><br>
				<textarea id="comment-text" name="content" cols="30" rows="10" style="width: 100%; height: 150px; color: black;"></textarea>
				<div id="captcha-wrapper" style="margin: 10px 0;">
					<img src="<?=SITE_URL?>get-captcha-for-comment/" alt="captcha" id="captcha" class="pointer captcha-reload"> 
					<span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span> 
					<?=__('input captcha')?>: <input type="text" id="captcha-code" name="captcha_code">
				</div>
				<input type="submit" value="<?=__('add comment')?>" class="btn" style="margin-top: 5px;">
			</div>
		</form>
	</div>
</div>
<?php endif;?>