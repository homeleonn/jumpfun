<?php
	$terms = $this->di->get('db')->getAll('Select t.*, tt.* from terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and tr.object_id = ' . $id);
	//var_dump(get_defined_vars());
	
	$categories = $tags = '';
	if($terms){
		foreach($terms as $term){
			if($post_type . '-cat' == $term['taxonomy'])
				$categories .= "<a href='".SITE_URL."{$options['category_slug']}/{$term['slug']}/'>{$term['name']}</a>";
			elseif($post_type . '-tag' == $term['taxonomy'])
				$tags .= "<a href='".SITE_URL."{$options['tag_slug']}/{$term['slug']}/'>{$term['name']}</a>";
				
		}
	}
	
	if($categories)
		$categories = 'Категории: ' . $categories;
	if($tags)
		$tags = '<br>Теги: ' . $tags;
?>

<div class="container">
	<div style="float: left; padding: 10px;">
		<img src="<?=THEME . 'img/news_thumb.jpg'?>">
	</div>
	<?=$categories . $tags?>
	<div style="padding: 10px;"><?=$content?></div>
	
</div>