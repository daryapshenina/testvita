<?php
// DAN 2013
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Заполнение полей для договора:</div>
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
	
			// Реквизиты для договора 
			if ($setting_name == "contract")
			{
				$contract_checked = $setting_parameter;
				if ($contract_checked == "0"){$contract_checked_0 = "checked";} else {$contract_checked_0 = "";}
				if ($contract_checked == "1"){$contract_checked_1 = "checked";} else {$contract_checked_1 = "";}			
			} 			

		endwhile;	
		
		
		
	// вывод параметров	
	echo'	
		<form method="POST" action="http://'.$site.'/admin/com/shop/contractupdate">			
		<table class="w100_bs1">
			<tr>
				<td class="cell-v ">&nbsp;</td>
				<td class="cell-title-modules">&nbsp;</td>
				<td class="cell-desc-modules">&nbsp;</td>
			</tr>		
			<tr>
				<td class="cell-v ">&nbsp;</td>
				<td class="cell-title-modules shop-tab-title">&nbsp;</td>
				<td class="cell-desc-modules"><span class="lineheight20"><b>ЗАПОЛНЯТЬ ДАННЫЕ ДЛЯ ДОГОВОРА:</b></span></td>
			</tr>
			<tr>
				<td class="cell-v ">1</td>
				<td class="cell-title-modules"><b>Формирование договора</b></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="0" '.$contract_checked_0.' name="contract">Отключить<br/>
						<input type="radio" value="1" '.$contract_checked_1.' name="contract">Включить<br/>						
					</span>					
				</td>
			</tr>				
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;			
		</form>		
	';	




	
	
	
	

		
}
?>