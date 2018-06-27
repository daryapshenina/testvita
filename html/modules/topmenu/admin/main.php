<?php
defined('AUTH') or die('Restricted access');

$act = $admin_d3;

$module_pub = intval($_POST["pub"]);
$module_content = htmlspecialchars($_POST["content"]);
$bt = $_POST["bt"]; // кнопка 'Отменить'

// Условие - отменить
if ($bt == "Выход"){	Header ("Location: /admin/modules/"); exit;} 

// Условие публикации
if (!isset($module_pub) || $module_pub == ""){$module_pub = "0";} else{$module_pub = "1";}  

function a_com()
{ 
	global $site; 
	// вывод содержимого модуля	
	$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'topmenu'") or die ("Невозможно сделать выборку из таблицы - 1");
	
	while($m = mysql_fetch_array($num)):
		$module_id = $m['id'];
		$module_title = $m['title'];
		$module_pub = $m['pub'];	
		$module_enabled = $m['enabled'];
		$module_description = $m['description'];
		$module_content = $m['content'];			
	endwhile;
		
	// устанавливаем признак публикации
	if ($module_pub == 1){$pub = "checked";} else{$pub = "";} 
	
	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($module_enabled == "1")
	{
		echo '
		<h1><img border="0" src="/modules/topmenu/admin/images/ico.png" style="width:25px; height:25px; float:left; padding-top:2px;" >&nbsp;&nbsp;Модуль "Верхнее меню"</h1>
		
		<form method="POST" action="/admin/modules/topmenu/update/">	
		
		<table class="admin_table_2">
			<tr>	
				<td style="width:200px;">Название модуля</td>
				<td><b>'.$module_title.'</b></td>
			</tr>				
			<tr>		
				<td>Описание модуля</td>
				<td>'.$module_description.'</td>
			</tr>				
		</table>
		
		<br/>
		&nbsp;&nbsp;<input class="yellowbutton" type="submit" value="Выход" name="bt">
		<br/>
		&nbsp;
		</form>	
		';
	} // конец проверки 'enabled'
	else 
	{			
		echo '<div id="main-top">Модуль "topmenu" не подключён</div>';
	}
} // конец функции


?>