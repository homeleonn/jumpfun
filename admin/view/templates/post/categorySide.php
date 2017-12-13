<div id="post-category" class="side-block">
	<div class="block-title">Категории <?=$options['common']?></div>
	<div class="block-content">
		<div id="category">
			<?php 
				if(isset($data['_category'])):
					foreach($data['_category'] as $_category):?>
						<label><input type="checkbox" name="_categories[]" value="<?=$_category?>" /> <?=$_category?></label><br>
			<?php 
					endforeach;
				endif;
			?>
		</div>
		<div><input type="text" id="new-category"></div>
		<div><input type="button" value="Добавить новую категорию" onclick="addTerm('category')"></div>
	</div>
</div>