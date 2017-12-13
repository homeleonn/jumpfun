<div id="post-tag" class="side-block">
	<div class="block-title">Метки <?=$options['common']?></div>
	<div class="block-content">
		<div id="tag">
			<?php 
				if(isset($data['_tag'])):
					foreach($data['_tag'] as $_tag):?>
						<label><input type="checkbox" name="_tags[]" value="<?=$_tag?>" /> <?=$_tag?></label><br>
			<?php 
					endforeach;
				endif;
			?>
		</div>
		<div><input type="text" id="new-tag"></div>
		<div><input type="button" value="Добавить новую метку" onclick="addTerm('tag')"></div>
	</div>
</div>