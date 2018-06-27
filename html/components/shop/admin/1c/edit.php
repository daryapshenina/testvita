<?php
defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $SITE, $shopSettings;

	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $protocol = 'https://';
		else $protocol = 'http://';
		
	if($shopSettings->c1_db_reset == 1){$reset_check = 'checked';} else{$reset_check = '';}
		
	echo '
		<h1>Настройки сайта обмена с 1С:</h1>
		<form method="POST" action="/admin/com/shop/1c/update">			
		<table class="admin_table_1">
			<tr>
				<th style="width:50px;"></th>
				<th style="width:200px;">Параметр</th>
				<th style="width:500px;">Значение</th>			
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>	
			<tr>
				<td>1</td>
				<td><b>Выгрузка &quot;Адрес сайта&quot;</b>:</td>
				<td><div>'.$protocol.$SITE->domain.'/components/shop/1c/1c_exchange.php</div></td>
			</tr>
			<tr>
				<td>2</td>
				<td><b>Имя пользователя:</b></td>
				<td><div>admin</div></td>
			</tr>			
			<tr>
				<td>3</td>
				<td><b>Пароль:</b></td>
				<td><input  required pattern="[a-zA-Z0-9]{6,20}" type="text" name="1c_psw" size="10" value="'.$shopSettings->c1_psw.'" title="От 6 до 20 символов a-z 0-9"></td>
			</tr>
		';
		
		if(isset($shopSettings->c1_import) && $shopSettings->c1_import == 1) // отображать импорт 1с
		{
			echo'
				<tr>
					<td>3a</td>
					<td><b>Импорт из 1С версии 7.7</b></td>
					<td><a href="/admin/com/shop/1cimport"><input class="greenbutton" type="button" value="Импорт" name="bt"></a></td>
				</tr>		
			';
		}
		
		echo'
			<tr>
				<td>4</td>
				<td><b>Разрешить стирать базу:</b></td>
				<td><input type="checkbox" name="1c_db_reset" value="1" '.$reset_check.'> При полной выгрузке из 1С - база стирается и загружается полностью с нуля.</td>
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