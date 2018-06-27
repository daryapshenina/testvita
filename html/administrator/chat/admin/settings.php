<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST['submit']))
{
	// Смотрим включить или нет чат
	if ($_POST['chat_on_off'] == 1){$chat_on = 1;}
	else {$chat_on = 0;}
	// Смотрим соответствует номер шаблона существующим
	if ($_POST['theme'] >= 1 OR $_POST['theme'] <= 9)
	{
		$chat_theme_db = $_POST['theme'];
	}
	else
	{
		$chat_theme_db = 1;
	}

	// Записываем все в бд
	$stmt_update_on = $db->prepare("UPDATE chat_settings SET parametr = :chat_on WHERE name = 'included'");
	$stmt_update_on->execute(array('chat_on' => $chat_on));

	$stmt_update_theme = $db->prepare("UPDATE chat_settings SET parametr = :theme WHERE name = 'theme'");
	$stmt_update_theme->execute(array('theme' => $chat_theme_db));

	// Перекидываем на главную админки
	header("Location: /admin");
}
else
{
	function a_com()
	{
		global $site, $db;

		// Включен ли чат?
		$stmt_chat_on = $db->query("SELECT * FROM chat_settings WHERE name = 'included' LIMIT 1");
		$setting_chat_on = $stmt_chat_on->fetch();

		if ($setting_chat_on['parametr'] == 1)
		{
			$settings_chat_on_checked = 'checked="checked"';
		}

		// Какая тема включена?
		$stmt_chat_theme = $db->query("SELECT * FROM chat_settings WHERE name = 'theme'");
		$setting_chat_theme = $stmt_chat_theme->fetch();

		switch ($setting_chat_theme['parametr'])
		{
			case 1: $settings_chat_theme_1 = 'checked="checked"';
				break;
			case 2: $settings_chat_theme_2 = 'checked="checked"';
				break;
			case 3: $settings_chat_theme_3 = 'checked="checked"';
				break;
			case 4: $settings_chat_theme_4 = 'checked="checked"';
				break;
			case 5: $settings_chat_theme_5 = 'checked="checked"';
				break;
			case 6: $settings_chat_theme_6 = 'checked="checked"';
				break;
			case 7: $settings_chat_theme_7 = 'checked="checked"';
				break;
			case 8: $settings_chat_theme_8 = 'checked="checked"';
				break;
			case 9: $settings_chat_theme_9 = 'checked="checked"';
				break;
			default: $settings_chat_theme_1 = 'checked="checked"';
		}

		// Выводим
		echo '
			<h1>Настройки сайта:</h1>
			<form method="POST" action="">
			<table class="admin_table" style="background-color:#ffffff;">
				<tr>
					<th style="widht:50px;"></th>
					<th style="widht:200px;">Параметр</th>
					<th>Значение</th>
				</tr>
				<tr>
					<td>1</td>
					<td><b>Включить чат:</b></td>
					<td><input type="checkbox" name="chat_on_off" value="1" '.$settings_chat_on_checked.' /></td>
				</tr>
				<tr>
					<td>2</td>
					<td><b>Тема формления:</b></td>
					<td>
						<table border="0" cellpadding="0" style="border-collapse: collapse" class="table_theme">
							<tr>
								<td><label for="theme_1" class="label_style"><img src="/administrator/chat/admin/images/theme/green.png" /></label></td>
								<td><label for="theme_2" class="label_style"><img src="/administrator/chat/admin/images/theme/azure.png" /></label></td>
								<td><label for="theme_3" class="label_style"><img src="/administrator/chat/admin/images/theme/black.png" /></label></td>
								<td><label for="theme_4" class="label_style"><img src="/administrator/chat/admin/images/theme/blue.png" /></label></td>
								<td><label for="theme_5" class="label_style"><img src="/administrator/chat/admin/images/theme/orange.png" /></label></td>
								<td><label for="theme_6" class="label_style"><img src="/administrator/chat/admin/images/theme/purple.png" /></label></td>
								<td><label for="theme_7" class="label_style"><img src="/administrator/chat/admin/images/theme/red.png" /></label></td>
								<td><label for="theme_8" class="label_style"><img src="/administrator/chat/admin/images/theme/turquoise.png" /></label></td>
								<td><label for="theme_9" class="label_style"><img src="/administrator/chat/admin/images/theme/yellow.png" /></label></td>
							</tr>
							<tr>
								<td align="center"><input type="radio" name="theme" id ="theme_1" value="1" '.$settings_chat_theme_1.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_2" value="2" '.$settings_chat_theme_2.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_3" value="3" '.$settings_chat_theme_3.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_4" value="4" '.$settings_chat_theme_4.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_5" value="5" '.$settings_chat_theme_5.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_6" value="6" '.$settings_chat_theme_6.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_7" value="7" '.$settings_chat_theme_7.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_8" value="8" '.$settings_chat_theme_8.' /></td>
								<td align="center"><input type="radio" name="theme" id ="theme_9" value="9" '.$settings_chat_theme_9.' /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<br />
			&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="submit">
			</form>
		';
	}
}

?>