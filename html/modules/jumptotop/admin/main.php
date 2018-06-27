<?php
// DAN обновление - январь 2014
// Кнопка для перехода в начало сайта

/*
	p1 - Размер кнопки
	p2 - Отступ слева
	p3 - Отступ снизу
	p4 - Цвет кнопки
*/

defined('AUTH') or die('Restricted access');

$head->addFile('/modules/jumptotop/frontend/style.css');
$head->addFile('/modules/jumptotop/admin/style.css');

// id модуля
$mod_id  = intval($admin_d3);

// действие
$act = $admin_d4;

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;}

// выбираем действие над модулем
if ($act == "update")
{
	$mod_pub = intval($_POST["pub"]);
	$mod_block = htmlspecialchars($_POST["block"]);
	$mod_size = intval($_POST["size"]);
	$mod_left = intval($_POST["left"]);
	$mod_bottom = intval($_POST["bottom"]);
	$mod_color = htmlspecialchars($_POST["color"]);

	// Условие публикации
	if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";} else{$mod_pub = "1";}

	$SQL_UPDATE = "UPDATE modules SET
						pub = :pub,
						p1 = :p1,
						p2 = :p2,
						p3 = :p3,
						p4 = :p4,
						block = :block
					WHERE id = :id
					LIMIT 1";

	$SQL_ARG = array(
		':pub' => $mod_pub,
		':p1' => $mod_size,
		':p2' => $mod_left,
		':p3' => $mod_bottom,
		':p4' => $mod_color,
		':block' => $mod_block,
		':id' => $mod_id
	);

	$st = $db->prepare($SQL_UPDATE);
	$st->execute($SQL_ARG);

	if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
	else {Header ("Location: /admin/modules/jumptotop/".$mod_id); exit;}
}
else {

	function a_com()
	{
		global $root, $site, $mod_id, $db;

		$arrayData = $db->query("SELECT * FROM `modules` WHERE `module` = 'jumptotop'");

		foreach($arrayData as $i)
		{
			$module_id = $i['id'];
			$module_pub = $i['pub'];
			$module_enabled = $i['enabled'];
			$module_description = $i['description'];
			$module_p1 = $i['p1'];
			$module_p2 = $i['p2'];
			$module_p3 = $i['p3'];
			$module_p4 = $i['p4'];
			$module_block = $i['block'];
		}

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

		// ДЕМО
		$frontend_edit = 0;
		$modules_pub = 1;
		$modules_p1 = $module_p1;
		$modules_p2 = $module_p2;
		$modules_p3 = $module_p3;
		$modules_p4 = $module_p4;
		include $root."/modules/jumptotop/frontend/main.php";

		// устанавливаем признак публикации
		if ($module_pub == 1){$pub = "checked";} else{$pub = "";}

		// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
		if ($module_enabled == "1")
		{
			echo '
			<div class="container">
			<h1><img border="0" src="/modules/jumptotop/admin/images/ico.png" style="float:left; margin-right:10px;" />Кнопка для перехода в начало сайта</h1>

			<script>

				function size_value()
				{
					var value = document.getElementById("size").value;
					document.getElementById("size_out").innerHTML = value;
					document.getElementById("mod_jumptotop_main").style.width = value+"px";
					document.getElementById("mod_jumptotop_main").style.height = value+"px";
				}

				function left_value()
				{
					var value = document.getElementById("c_left").value;
					document.getElementById("left_out").innerHTML = value;
					document.getElementById("mod_jumptotop_main").style.left = value+"px";
				}

				function bottom_value()
				{
					var value = document.getElementById("c_bottom").value;
					document.getElementById("bottom_out").innerHTML = value;
					document.getElementById("mod_jumptotop_main").style.bottom = value+"px";
				}

				function color_value()
				{
					var value = document.getElementById("color").value;
					document.getElementById("mod_jumptotop_main").style.backgroundColor = value;
				}

			</script>

			<form method="POST" action="/admin/modules/jumptotop/'.$mod_id.'/update">

			<table class="admin_table_2">
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
					<td width="200" height="25">Позиция вывода, блок</td>
					<td>
						<select class="input" size="1" name="block">
						'.$block_option.'
						</select>
						&nbsp;Определяет в каком месте (блоке) сайта вывести данный модуль
					</td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">Размер кнопки</td>
					<td><input id="size" type="range" min="40" max="100" step="1" name="size" value="'.$module_p1.'" onmousemove="size_value();"><span id="size_out" style="font-size:32px; margin-left:20px;"></span></td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">Отступ слева</td>
					<td><input id="c_left" type="range" min="0" max="100" step="1" name="left" value="'.$module_p2.'" onmousemove="left_value();"><span id="left_out" style="font-size:32px; margin-left:20px;"></span></td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">Отступ снизу</td>
					<td><input id="c_bottom" type="range" min="0" max="100" step="1" name="bottom" value="'.$module_p3.'" onmousemove="bottom_value();"><span id="bottom_out" style="font-size:32px; margin-left:20px;"></span></td>
				</tr>
				<tr>
					<td width="20">&nbsp;</td>
					<td width="200" height="25">Цвет кнопки</td>
					<td><input id="color" type="color" name="color"  value="'.$module_p4.'" onchange="color_value();"></td>
				</tr>
			</table>
			<input type="hidden" name="id" value="1" '.$module_id.' >
			<div style="margin-top:40px;">
			<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
			</div>
			&nbsp;
			</form>
			</div>
			<script type="text/javascript">size_value();left_value();bottom_value();color_value();</script>
			';
		} // конец проверки 'enabled'
		else
		{
			echo '<div id="main-top">Модуль <b>"Кнопка для перехода в начало сайта"</b> не подключён</div>';
		}
	} // конец функции
}

?>
