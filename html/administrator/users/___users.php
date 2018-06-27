<?php
// DAN 2010
// Настройки сайта
defined('AUTH') or die('Restricted access');

$none = $_POST["none"]; // кнопка 'Отменить'

// Условие - отменить
if ($none == "Отменить"){	Header ("Location: http://".$site."/admin"); exit;} 

function a_com()
{ 
	global $site;
	
	echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/administrator/tmp/images/users.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Пользователи сайта:</div>
		<div>&nbsp;</div>

		<form method="POST" action="http://'.$site.'/admin/users/update">	
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v">№</td>
				<td class="cell-title-modules">Логин</td>
				<td class="cell-desc-modules">Описание</td>
				<td  class="cell-pub" title="Активация. Зелёным цветом обозначены активные пользователи, серым - неактивные"align="center">Ак.</td>				
			</tr>
		</table>		
	';	
	
		// вывод модулей	
		$num = mysql_query("SELECT * FROM users") or die ("Невозможно сделать выборку из таблицы - 1");
			
		while($m = mysql_fetch_array($num)):
			$users_id = $m['id'];
			$users_login = $m['login'];
			$users_description = $m['description'];
			$users_psw = $m['psw'];	
			$users_level = $m['level'];	
			$users_active = $m['active'];	
			
			// --- условия активации ---
			if ($users_active == "1") 
			{
				$active = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$classmenu = "menu_pub";
			}
			else 
			{
				$active = '<img border="0" src="http://'.$site.'/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$classmenu = "menu_unpub";
			}			
			
	// вывод параметров	
		echo'		
			<table class="w100_bs1">		
				<tr>
					<td class="cell-v ">'.$users_id.'</td>
					<td class="cell-title-modules '.$classmenu.'"><b>'.$users_login.'</b></td>
					<td class="cell-desc-modules '.$classmenu.'">'.$users_description.'</td>
					<td class="cell-pub">'.$active.'</td>					
				</tr>
			</table>			
		';				
			
		endwhile;		
		
	// вывод параметров	
		echo'	
			<br/>
			&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
			<br/>
			&nbsp;			
			</form>				
		';			
	
		
} // конец функции компонента
?>