<input type="button" id="item-factory" value="Добавить">
<input type="submit" id="" value="Добавить">

<!-- Block for add post categories -->
<div id="post-category" class="side-block">
	<div class="block-title">Категории <?=$options['common']?></div>
	<div class="block-content">
		<div id="category">
			<?php 
				/*if(isset($data['_category'])):
					foreach($data['_category'] as $categoryId => $categoryData):?>
						<label><input type="checkbox" name="_categories[]" value="<?=$categoryData['slug']?>" /> <?=$categoryData['name']?></label><br>
			<?php 
					endforeach;
				endif;*/
			?>
			
			<?php 
				if(isset($data['terms'])):
					foreach($data['terms'] as $categoryData):
						if($categoryData['taxonomy'] != $options['category_slug']) continue;
			?>
						<label><input type="checkbox" name="_categories[]" value="<?=$categoryData['slug']?>" /> <?=$categoryData['name']?></label><br>
			<?php 
					endforeach;
				endif;exit;
			?>
		</div>
		<div><input type="text" id="new-category"></div>
		<div><input type="button" value="Добавить новую категорию" onclick="addTerm('category')"></div>
	</div>
</div>

<!-- Block for add post tags -->
<div id="post-tag" class="side-block">
	<div class="block-title">Метки <?=$options['common']?></div>
	<div class="block-content">
		<div id="tag">
			<?php 
				/*if(isset($data['_tag'])):
					foreach($data['_tag'] as $tagId => $tagData):?>
						<label><input type="checkbox" name="_tags[]" value="<?=$tagData['slug']?>" /> <?=$tagData['name']?></label><br>
			<?php 
					endforeach;
				endif;*/
			?>
			
			<?php 
				if(isset($data['_tag'])):
					foreach($data['_tag'] as $tagId => $tagData):?>
						<label><input type="checkbox" name="_tags[]" value="<?=$tagData['slug']?>" /> <?=$tagData['name']?></label><br>
			<?php 
					endforeach;
				endif;
			?>
		</div>
		<div><input type="text" id="new-tag"></div>
		<div><input type="button" value="Добавить новую метку" onclick="addTerm('tag')"></div>
	</div>
</div>