<?php if(!isset($data['listForParents'])) return; ?>
<!-- Block for post properties -->
<div id="post-properties" class="side-block">
	<div class="block-title">Свойства <?=(isset($_GET['term']) ? 'термина' : 'страницы')?></div>
	<div class="block-content">
		<div>Родительская</div>
		<div><?=$data['listForParents']?></div>
	</div>
</div>