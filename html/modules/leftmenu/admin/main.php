<?php
// Левое меню
defined('AUTH') or die('Restricted access');

$act = $admin_d3;

if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else{$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else{$mod_pub = '';}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else{$mod_titlepub = 0;}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else{$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else{$mod_ordering = 0;}
if(isset($_POST["menuout"])){$menu_out = intval($_POST["menuout"]);} else{$menu_out = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else{$bt_save = '';} // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else{$bt_prim = '';} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';} // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;} 

// Условие публикации
if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";} else{$mod_pub = "1";} 

// Порядок следования
if ($mod_ordering > 999){$mod_ordering = 999;}
if ($mod_ordering < 0){$mod_ordering  = 0;}

// Вывод меню
if ($menu_out == 0){$menu_out = "1";}


// выбираем действие над модулем
if ($act == "update")
{
	// Обновляем данные в таблице "modules"
	$stmt_update = $db->prepare("UPDATE modules SET title = :title, pub = :pub, titlepub = :titlepub, block = :block, ordering = :ordering, p1 = :p1  WHERE module = 'leftmenu' LIMIT 1 ;");
	$stmt_update->execute(array('title' => $mod_title, 'pub' => $mod_pub, 'titlepub' => $mod_titlepub, 'block' => $mod_block, 'ordering' => $mod_ordering, 'p1' => $menu_out));
	
	if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
	else {Header ("Location: /admin/modules/leftmenu"); exit;}
}
else 
{  

	function a_com()
	{ 
		global $db, $domain; 
		
		$stmt_modules = $db->query("SELECT * FROM modules WHERE module = 'leftmenu'");

		$menuout = array_fill(1, 5, ''); // Заполняем массив пустыми значениями	
		
		while($n = $stmt_modules->fetch())
		{
			$module_id = $n['id'];
			$module_title = $n['title'];
			$module_pub = $n['pub'];
			$module_titlepub = $n['titlepub'];			
			$module_enabled = $n['enabled'];
			$module_description = $n['description'];
			$module_content = $n['content'];
			$module_block = $n['block'];
			$module_ordering = $n['ordering'];
			$m = $n['p1'];
			$menuout[$m] = 'selected';			
		}

		
		// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
		$block_option = '';
		
		$stmt_block = $db->query("SELECT * FROM block");	
		if($stmt_block->rowCount() > 0) 
		{	
			while($b = $stmt_block->fetch())
			{
				$b_id = $b['id'];
				$b_name = $b['block'];	
				$b_description = $b['description'];
			
				if ($b_name == $module_block){$selected = 'selected';} else {$selected = '';}
				$block_option .= '<option '.$selected.' value='.$b_name.'>'.$b_description.'</option>';	
			}				
		}			
		// ======== / загрузка блоков вывода =======			
			
		// устанавливаем признак публикации
		if ($module_pub == 1){$pub = "checked";} else{$pub = "";} 
		
		// устанавливаем признак публикации заголовка молуля
		if ($module_titlepub == 1){$titlepub = "checked";} else{$titlepub = "";} 		
		
		// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
		if ($module_enabled == "1")
		{
			echo '
			<div class="container">
				<h1><img border="0" src="/modules/leftmenu/admin/images/ico.png" style="float:left; margin-right:10px;"/>Модуль "Левое меню"</h1>
			
				<form method="POST" action="/admin/modules/leftmenu/update/">	
				
				<table class="admin_table_2">	
					<tr>	
						<td style="width:200px">Название модуля</td>
						<td><input class="input" type="text" name="title" size="20" value="'.$module_title.'"></td>
					</tr>		
					<tr>		
						<td>Описание модуля</td>
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
							<select class="input" name="block">
							'.$block_option.'
							</select>
							&nbsp;Определяет в каком месте (блоке) сайта вывести данный модуль
						</td>
					</tr>
					<tr>	
						<td>Порядок следования</td>
						<td><input class="input" type="number" name="ordering" value="'.$module_ordering.'"/ style="width:80px;"></td>
					</tr>
					<tr>		
						<td>Отображение меню</td>
						<td>
							<select class="input" name="menuout">
								<option '.$menuout[1].' value="1">Развёрнутое меню</option>
								<option '.$menuout[2].' value="2">Свёрнутое меню</option>
								<option '.$menuout[3].' value="3">Меню - "аккордион"</option>
								<option '.$menuout[4].' value="4">Выдвигающееся меню</option>
								<option '.$menuout[5].' value="5">Лист</option>								
							</select>
						</td>
					</tr>				
				</table>
				<div style="margin:40px 0px 60px 0px">
				<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
				</div>
				</form>
			</div>
			';
		} // конец проверки 'enabled'
		else 
		{			
			echo '<div id="main-top">Модуль "leftmenu" не подключён</div>';
		}
	} // конец функции
}

?>