<div class="container">
	<?=$content?>
	<ul>
		<?php
		if($this->haveChild($id)):
			while($child = $this->theChild()):
				echo '<li><a href="'.FULL_URL_WITHOUT_PARAMS . $child['url'] .'/">'.$child['title'].'</a></li>';
			endwhile;
		endif;
		?>
	</ul>
</div>
