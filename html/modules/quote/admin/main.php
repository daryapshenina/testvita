<?php
// DAN 2011
// редактируем страницу, определённую переменной $admin_d4 = $d[4];

defined('AUTH') or die('Restricted access');

$act = $admin_d3;

$mod_title = htmlspecialchars($_POST["title"]);
$mod_pub = intval($_POST["pub"]);
$mod_titlepub = intval($_POST["titlepub"]);
$mod_block = htmlspecialchars($_POST["block"]);
$mod_ordering = intval($_POST["ordering"]);
$none = $_POST["none"]; // кнопка 'Отменить'

// Условие - отменить
if ($none == "Отменить"){	Header ("Location: http://".$site."/admin/modules/"); exit;} 

// Условие публикации
if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";} else{$mod_pub = "1";} 

// выбираем действие над модулем
if ($act == "update")
{
	// Обновляем данные в таблице "modules"
	$query_update_module_quote = "UPDATE `modules` SET `title` = '$mod_title', `pub` = '$mod_pub', `titlepub` = '$mod_titlepub', `block` = '$mod_block', `ordering` = '$mod_ordering' WHERE `module` = 'quote' LIMIT 1 ;";
			
	$sql_module_quote = mysql_query($query_update_module_quote) or die ("Невозможно обновить данные");
	
	Header ("Location: http://".$site."/admin/modules/"); exit;	
}
else { 	

function a_com()
{ 
	global $site; 
	// вывод содержимого модуля	
	$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'quote'") or die ("Невозможно сделать выборку из таблицы - 1");
	
	while($m = mysql_fetch_array($num)):
		$module_id = $m['id'];
		$module_title = $m['title'];
		$module_pub = $m['pub'];
		$module_titlepub = $m['titlepub'];	
		$module_enabled = $m['enabled'];
		$module_block = $m['block'];
		$module_ordering = $m['ordering'];			
	endwhile;
	
		
	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========		
	$block_query = mysql_query("SELECT * FROM `block`") or die ("Невозможно сделать выборку из таблицы - 1");	
	$result = mysql_num_rows($block_query);		
	if ($result > 0) 
	{	
		while($b = mysql_fetch_array($block_query)):
			$b_id = $b['id'];
			$b_name = $b['block'];	
			$b_description = $b['description'];
		
		if ($b_name == $module_block){$selected = 'selected';} else {$selected = '';}
		$block_option .= '<option '.$selected.' value='.$b_name.'>'.$b_description.'</option>';	
		endwhile;				
	}			
	// ======== / загрузка блоков вывода =======	


	// устанавливаем признак публикации
	if ($module_pub == 1){$pub = "checked";} else{$pub = "";} 
	
	// устанавливаем признак публикации заголовка модуля
	if ($module_titlepub == 1){$titlepub = "checked";} else{$titlepub = "";} 		
	
	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($module_enabled == "1")
	{
		echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/modules/quote/admin/images/ico.png" width="25" height="25"  style="float: left; padding-top: 2px;" />&nbsp;&nbsp;Модуль "Цитата"</div>
		
		<div class="padding-horizontal-20">
	
		<form method="POST" action="http://'.$site.'/admin/modules/quote/update/">	
		
		<table class="main-tab">
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>	
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Название модуля</td>
				<td><input type="text" name="title" size="20" value="'.$module_title.'"></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Описание</td>
				<td>'.$module_description.'</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать модуль</td>
				<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Опубликовать заголовок модуля</td>
				<td><input type="checkbox" name="titlepub" value="1" '.$titlepub.' ></td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Позиция вывода, блок</td>
				<td>
					<select size="1" name="block">
					'.$block_option.'
					</select>
					&nbsp;Определяет в каком месте (блоке) сайта вывести данный модуль
				</td>
			</tr>				
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Порядок следования</td>
				<td><input type="text" name="ordering" size="3" value="'.$module_ordering.'"></td>
			</tr>
		</table>			
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;
		</form>
		</div>
	';
	} // конец проверки 'enabled'
	else 
	{			
		echo '<div id="main-top">Модуль "quote" не подключён</div>';
	}
} // конец функции
}

?>