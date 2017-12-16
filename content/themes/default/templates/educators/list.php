<?php
//var_dump(get_defined_vars());exit;
if(!isset($educators_list) || empty($educators_list)) {
	echo 'Педагогов нет!';
	return;
}

?>

<div class="list-wrapper container-fluid">
	<div class="col-sm-9" style="float: right;">
		<?php foreach($educators_list as $educator):?>

		<div class="col-sm-3 list-item">
			<div>
				<a href="<?=SITE_URL . $slug . '/' . $educator['url']?>/">
					<div class="thumb"><img src="<?=THEME . 'img/news_thumb.jpg'?>" alt="" width="100%"></div>
					<div class="name"><?=$educator['title']?></div>
				</a>
			</div>
		</div>

		<?php endforeach;?>
	</div>
	<div class="col-sm-3">
		<?php
			$filters = $this->di->get('db')->getAll('Select DISTINCT t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and tt.count > 0 and (tt.taxonomy = \'educator-cat\' OR tt.taxonomy = \'educator-tag\')');
			
			if($filters){
				echo '<a href="', SITE_URL ,  $slug, '/">Cбросить фильтр</a><br>';
				foreach($filters as $filter){
					echo '<a href="', SITE_URL , $filter['taxonomy'], '/' . $filter['slug'], '/">', $filter['name'], '</a><br>';
				}
			}
			
		
		?>
	</div>
	
</div>