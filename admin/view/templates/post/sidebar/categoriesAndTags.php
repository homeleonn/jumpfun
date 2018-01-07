<?php 
use Jump\helpers\Common;
//var_dump($data, $options);exit;
if(Common::isPage()) return;

if(isset($options['taxonomy'])):
	foreach($options['taxonomy'] as $taxonomy => $taxValues):
?>

<!-- Block for add post <?=$taxonomy?> -->
<div id="post-<?=$taxonomy?>" class="side-block">
	<div class="block-title"><?=$taxValues['title']?></div>
	<div class="block-content">
		<div id="term-<?=$taxonomy?>">
			<?php 
				if(isset($data['terms'])):
					foreach($data['terms'] as $key => $termData):
						if($termData['taxonomy'] != $taxonomy) continue;
						$checked = isset($data['selfTerms']) && in_array($termData['id'], $data['selfTerms']) ? 'checked' : '';
			?>
						<label><input type="checkbox" name="terms[<?=$taxonomy?>][]" value="<?=$termData['id']?>" <?=$checked?> /> <?=$termData['name']?></label><br>
			<?php 
						unset($data['terms'][$key]);
					endforeach;
				endif;
			?>
		</div>
		<div><input type="text" id="new-<?=$taxonomy?>"></div>
		<div><input type="button" value="<?=$taxValues['add']?>" onclick="addTerm('<?=$taxonomy?>')"></div>
	</div>
</div>

<?php
	endforeach;
endif;
?>