<?php
defined('AUTH') or die('Restricted access');
include_once $root.'/components/account/classes/accountSettings.php';
$head->addFile('/components/account/admin/settings/settings.css');


function a_com()
{
	global $root, $db, $domain;

	$settings = accountSettings::getInstance();

	if($settings->registration_allow) $registration_allow_checked = 'checked';
		else $registration_allow_checked = '';
		
	if($settings->shop_allow) $shop_allow_checked = 'checked';
		else $shop_allow_checked = '';

	if($settings->ads_allow) $ads_allow_checked = 'checked';
		else $ads_allow_checked = '';		

	echo '
		<h1>Настройки аккаунта пользователей:</h1>
		<form method="POST" action="/admin/com/account/settings/update">
		<table class="admin_table">
			<tr>
				<th style="width:50px">&nbsp;</th>
				<th style="width:250px">Параметр</th>
				<th>Значение</th>
			</tr>
			<tr>
				<td></td>
				<td>Разрешить регистрацию пользователей</td>
				<td><input id="registration_allow" class="input" type="checkbox" name="registration_allow" '.$registration_allow_checked.' value="1"><label for="registration_allow"></label></td>
			</tr>
			<tr>
				<td class="td_sep">&nbsp;</td>
				<td class="td_sep"><b>ДОСТУП К КОМПОНЕНТАМ:</b></td>
				<td class="td_sep">&nbsp;</td>
			</tr>			
			<tr>
				<td></td>
				<td>Интернет - магазин</td>
				<td><input id="shop_allow" class="input" type="checkbox" name="shop_allow" '.$shop_allow_checked.' value="1"><label for="shop_allow"></label></td>
			</tr>
			<tr>
				<td></td>
				<td>Доска обявлений</td>
				<td><input id="ads_allow" class="input" type="checkbox" name="ads_allow" '.$ads_allow_checked.' value="1"><label for="ads_allow"></label></td>
			</tr>			
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
		<br/>
		&nbsp;
		</form>
	';
}
?>
