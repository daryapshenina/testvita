<?php
// DAN 2012
// Настройки компонента архива статей

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Настройки компонента "Архив статей":</div>
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
		$num = mysql_query("SELECT * FROM `com_article_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
			
		while($m = mysql_fetch_array($num)):
			$setting_id = $m['id'];
			$setting_name = $m['name'];
			$setting_parameter = $m['parametr'];
			
			// Заголовок банка изображений 
			if ($setting_name == "quantity")
			{
				$quantity = '<input class="validate[required,length[1,5],custom[onlyNumber]" type="text" name="quantity" size="3" value="'.$setting_parameter.'">';
			} 			 						
			
		endwhile;		
		
	// вывод параметров	
		echo'	
			<form method="POST" action="http://'.$site.'/admin/com/article/settingsupdate/">			
			<table class="w100_bs1">		
				<tr>
					<td class="cell-v ">1</td>
					<td class="cell-title-modules"><span class="lineheight20"><b>Количество статей выводимых <br /> в разделе / категории</b></span></td>
					<td class="cell-desc-modules">'.$quantity.'</td>
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