<?php ?>
<table class="mytable user-list">
	<tr align="center">
		<td width="30%">Логин</td>
		<td>Email</td>
		<td>Роль</td>
		<td>Записи</td>
	</tr>
	<?php 
		foreach($data['users'] as $user):
	?>
	<tr class="user-cell" data-id="<?=$user['id']?>">
		<td style="font-size: 12px;">
			<div class="avatar"></div>
			<?=$user['login']?>
			<div class="options">
				[<span style="color: coral;" class="pointer edit">Изменить</span>] 
				[<span style="color: red;" class="pointer remove">Удалить</span>]
			</div>
		</td>
		<td>
			<a href="mailto:<?=$user['email']?>"><?=$user['email']?></a>
		</td>
		<td>
			<?=(isset($user['accesslevel']) && $user['accesslevel'] == 1 ? 'Администратор' : 'Подписчик')?>
		</td>
		<td></td>
	</tr>
	<?php endforeach; ?>
</table>