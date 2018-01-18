<?php 
	if(isset($content)){
		echo $content; 
		if($this->haveChild($id)): 
			echo '<ul>';
			while($child = $this->theChild()):
				echo '<li><a href="' . $child['url'] . '/">'.$child['title'].'</a></li>';
			endwhile;
			echo '<ul>';
		endif;
	}else{
		echo 'Не найдено';
	}
?>