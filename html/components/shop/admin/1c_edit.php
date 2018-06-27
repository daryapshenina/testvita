<?php
// DAN 2013
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Настройки сайта обмена с 1С:</div>
		<div>&nbsp;</div>
		
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v"></td>
				<td class="cell-title-modules" >Параметр</td>
				<td class="cell-desc-modules" >Значение</td>			
			</tr>
		</table>		
	';	
	
		// вывод настроек	
		$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
			
		while($m = mysql_fetch_array($num)):
			$setting_id = $m['id'];
			$setting_name = $m['name'];
			$setting_parameter = $m['parametr'];
	
			// Пароль 
			if ($setting_name == "1c_psw"){$psw_1c = $setting_parameter;}
			// Импорт товаров из 1с версии 7.7 
			if ($setting_name == "1c_import"){$import_1c = $setting_parameter;}
			
		endwhile;	
		
		
		
	// вывод параметров	
	echo'	
		<form method="POST" action="http://'.$site.'/admin/com/shop/1cupdate">			
		<table class="w100_bs1">
			<tr>
				<td class="cell-v ">&nbsp;</td>
				<td class="cell-title-modules">&nbsp;</td>
				<td class="cell-desc-modules">&nbsp;</td>
			</tr>
			<tr>
				<td class="cell-v ">1</td>
				<td class="cell-title-modules"><b>Выгрузка &quot;Адрес сайта&quot;</b>:</td>
				<td class="cell-desc-modules"><div>http://'.$site.'/components/shop/1c_exchange.php</div></td>
			</tr>
			<tr>
				<td class="cell-v ">2</td>
				<td class="cell-title-modules"><b>Имя пользователя:</b></td>
				<td class="cell-desc-modules"><div>admin</div></td>
			</tr>			
			<tr>
				<td class="cell-v ">3</td>
				<td class="cell-title-modules"><b>Пароль:</b></td>
				<td class="cell-desc-modules"><input  required pattern="[a-zA-Z0-9]{6,20}" type="text" name="psw_1c" size="10" value="'.$psw_1c.'"></td>
			</tr>
		';
		
		if($import_1c == 1) // отображать импорт 1с
		{
		echo'
			<tr>
				<td class="cell-v ">4</td>
				<td class="cell-title-modules"><b>Импорт из 1С версии 7.7</b></td>
				<td class="cell-desc-modules"><a href="http://'.$site.'/admin/com/shop/1cimport"><input class="greenbutton" type="button" value="Импорт" name="bt"></a></td>
			</tr>		
		';
		}
		
		echo'
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;			
		</form>		
	';			
}
?>