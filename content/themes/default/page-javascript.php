<div class="container">
<?php
if($this->haveChild($id)){
	while($child = $this->theChild()){
		echo '<a href="'.FULL_URL_WITHOUT_PARAMS . $child['url'] .'/">'.$child['title'].'</a><br>';
	}
}
?>
</div>
