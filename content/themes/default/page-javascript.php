<div class="container">
	<?=$content?>
	<?php 
		if($this->haveChild($id)): 
			echo '<ul>';
			while($child = $this->theChild()):
				echo '<li><a href="'.FULL_URL_WITHOUT_PARAMS . $child['url'] .'/">'.$child['title'].'</a></li>';
			endwhile;
			echo '<ul>';
		endif;
	?>
</div>
