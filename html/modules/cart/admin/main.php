<?php
// DAN обновление - январь 2014
// Корзина

defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($admin_d3);

// действие
$act = $admin_d4;

if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);}else{$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else{$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else {$mod_titlepub = 0;}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else {$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else {$mod_ordering = 0;}
if(isset($_POST["typeView"])){$mod_typeView = intval($_POST["typeView"]);} else {$mod_typeView = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;} 

// Условие публикации
if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";} else{$mod_pub = "1";} 

// выбираем действие над модулем
if ($act == "update")
{
	// Обновляем данные в таблице "modules"
	$query_updatedit_modeule_special = "UPDATE `modules` SET `title` = '$mod_title', `pub` = '$mod_pub', `titlepub` = '$mod_titlepub', `block` = '$mod_block', `ordering` = '$mod_ordering', `p1` = '$mod_typeView' WHERE `id` = '$mod_id' LIMIT 1 ;";
			
	$sql_module_special = mysql_query($query_updatedit_modeule_special) or die ("Невозможно обновить данные");
	
	if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
	else {Header ("Location: /admin/modules/cart/".$mod_id); exit;}
}
else {  

	function a_com()
	{ 
		global $site, $mod_id; 
		// вывод содержимого модуля	
		$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'cart'") or die ("Невозможно сделать выборку из таблицы - 1");
		
		while($m = mysql_fetch_array($num)):
			$module_id = $m['id'];
			$module_title = $m['title'];
			$module_pub = $m['pub'];
			$module_titlepub = $m['titlepub'];			
			$module_enabled = $m['enabled'];
			$module_description = $m['description'];
			$module_block = $m['block'];
			$module_ordering = $m['ordering'];	
			$module_typeView = $m['p1'];
		endwhile;
		
		// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========		
		$block_query = mysql_query("SELECT * FROM `block`") or die ("Невозможно сделать выборку из таблицы - 1");	
		$result = mysql_num_rows($block_query);
		$block_option = '';
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

		// Тип вывода, 0 обычный
		if($module_typeView == 0){$typeViewSelect0 = 'selected';} else{$typeViewSelect0 = '';}
		if($module_typeView == 1){$typeViewSelect1 = 'selected';} else{$typeViewSelect1 = '';}
		
		// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
		if ($module_enabled == "1")
		{
			echo '
			<div class="container">
				<h1><img border="0" src="/modules/cart/admin/images/ico.png" style="float:left; margin-right:10px;"/>Модуль Корзина</h1>
		
				<form method="POST" action="/admin/modules/cart/'.$mod_id.'/update">
				<table class="admin_table_2">
					<tr>	
						<td style="width:200px;">Название модуля</td>
						<td><input class="input" type="text" name="title" size="20" value="'.$module_title.'"></td>
					</tr>		
					<tr>			
						<td>Описание</td>
						<td>'.$module_description.'</td>
					</tr>
					<tr>		
						<td>Опубликовать модуль</td>
						<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
					</tr>
					<tr>			
						<td>Опубликовать заголовок</td>
						<td><input type="checkbox" name="titlepub" value="1" '.$titlepub.' ></td>
					</tr>
					<tr>			
						<td>Позиция вывода, блок</td>
						<td>
							<select class="input" size="1" name="block">
							'.$block_option.'
							</select>
							&nbsp;Определяет в каком месте (блоке) сайта вывести данный модуль
						</td>
					</tr>				
					<tr>		
						<td>Порядок следования</td>
						<td><input class="input" type="number" name="ordering" size="3" value="'.$module_ordering.'" style="width:80px;"></td>
					</tr>
					<tr>		
						<td>Вывод</td>
						<td>
							<select class="input" size="1" name="typeView">
								<option value="0" '.$typeViewSelect0.'>Обычный</option>
								<option value="1" '.$typeViewSelect1.'>Раскрывающийся</option>
							</select>
						</td>
					</tr>	
				</table>
				<input type="hidden" name="id" value="1" '.$module_id.' >
				<div style="margin-top:40px;">
				<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
				</div>
				</form>
			</div>
			';
		} // конец проверки 'enabled'
		else 
		{			
			echo '<div id="main-top">Модуль <b>"Редактируемый модуль"</b> не подключён</div>';
		}
	} // конец функции
}

?>