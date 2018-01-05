<?php 
use Jump\helpers\Common;
//var_dump($data, $options);exit;
if(Common::isPage()) return;

if(isset($options['taxonomy'])):
	foreach($options['taxonomy'] as $taxonomy):

?>

<!-- Block for add post categories -->
<div id="post-category" class="side-block">
	<div class="block-title"><?=$taxonomy . ' ' . $options['common']?></div>
	<div class="block-content">
		<div id="category">
			<?php 
				if(isset($data['terms'])):
					foreach($data['terms'] as $key => $termData):
						if($termData['taxonomy'] != $taxonomy) continue;
						$checked = isset($data['selfTerms']) && in_array($termData['id'], $data['selfTerms']) ? 'checked' : '';
			?>
						<label><input type="checkbox" name="_categories[]" value="<?=$termData['id']?>" <?=$checked?> /> <?=$termData['name']?></label><br>
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

<?php
	endforeach;
endif;
?>