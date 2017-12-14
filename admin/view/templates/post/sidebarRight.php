<!-- Block for add post categories -->
<div id="post-category" class="side-block">
	<div class="block-title">Категории <?=$options['common']?></div>
	<div class="block-content">
		<div id="category">
			<?php 
				if(isset($data['terms'])):
					foreach($data['terms'] as $key => $categoryData):
						if($categoryData['taxonomy'] != $options['category_slug']) continue;
						$checked = isset($data['selfTerms']) && in_array($categoryData['id'], $data['selfTerms']) ? 'checked' : '';
			?>
						<label><input type="checkbox" name="_categories[]" value="<?=$categoryData['id']?>" <?=$checked?> /> <?=$categoryData['name']?></label><br>
			<?php 
						unset($data['terms'][$key]);
					endforeach;
				endif;
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
				if(isset($data['terms'])):
					foreach($data['terms'] as $tagId => $tagData):
						$checked = isset($data['selfTerms']) && in_array($tagData['id'], $data['selfTerms']) ? 'checked' : '';
			?>
						<label><input type="checkbox" name="_tags[]" value="<?=$tagData['id']?>" <?=$checked?> /> <?=$tagData['name']?></label><br>
			<?php 
					endforeach;
				endif;
			?>
		</div>
		<div><input type="text" id="new-tag"></div>
		<div><input type="button" value="Добавить новую метку" onclick="addTerm('tag')"></div>
	</div>
</div>