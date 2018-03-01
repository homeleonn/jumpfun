User room, <?=session('user.name')?><br>
<?php
if(isAdmin()){
	echo '<a href="'.SITE_URL.'admin/">Администраторская</a>';
}
?>
<br>
<a href="<?=SITE_URL?>user/exit/">Выход</a>