<form action="<?=SITE_URL?>user/auth/" method="POST" id="loginform">
	<input type="hidden" name="token" value="<?=token()?>">
	Почта<br>
	<input type="text" name="email" id="email"><br>
	Пароль<br>
	<input type="password" name="pass" id="pass"><br>
	<input type="submit" value="Вход">
</form>