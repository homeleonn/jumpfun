<?php
//dd(get_defined_vars());
if(isset($data['empty'])): echo $data['empty']; else:
?>
<table class="mytable">
	<tr>
		<td width="20%">Плагин</td>
		<td>Описание</td>
	</tr>
	<?php foreach($data['plugins'] as $plugin):?>
	<tr class="option-cell">
		<td>
			<?=$plugin['Plugin Name']?>
			<div class="options">
				[<span style="color: coral;" class="pointer edit">Активировать</span>] 
				[<span style="color: red;" class="pointer remove">Удалить</span>]
			</div>
		</td>
		<td style="text-align: left;">
			<?=$plugin['Description']?>
			<div style="margin-top: 5px;">
				Версия <?=$plugin['Version']?> | 
				Автор: <a href="<?=$plugin['Author URI']?>"></a><?=$plugin['Author']?> | 
				<a href="<?=$plugin['Plugin URI']?>">Сайт плагина</a>
			</div>
		</td>
	<?php endforeach;?>
	</tr>
</table>
<?php
endif;
?>
