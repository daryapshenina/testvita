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
$quantity = intval($_POST["quantity"]);
$length = intval($_POST["length"]);

$none = $_POST["none"]; // кнопка 'Отменить'

// Условие - отменить
if ($none == "Отменить"){	Header ("Location: http://".$site."/admin/modules/"); exit;}

// Условие публикации
if (!isset($mod_pub) || $mod_pub == ""){$mod_pub = "0";} else{$mod_pub = "1";}

// выбираем действие над модулем
if ($act == "update")
{
	// Обновляем данные в таблице "modules"
	$query_update_topnotes = "UPDATE `modules` SET `title` = '$mod_title', `pub` = '$mod_pub', `titlepub` = '$mod_titlepub', `p1` = '$quantity', `p2` = '$length', `block` = '$mod_block', `ordering` = '$mod_ordering' WHERE `module` = 'topnotes' LIMIT 1 ;";

	$sql_topnotes = mysql_query($query_update_topnotes) or die ("Невозможно обновить данные");

	Header ("Location: http://".$site."/admin/modules/"); exit;
}
else {

	function a_com()
	{

	global $site, $quantity;

		// вывод содержимого модуля
		$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'topnotes'") or die ("Невозможно сделать выборку из таблицы - 1");

		while($m = mysql_fetch_array($num)):
			$module_id = $m['id'];
			$module_title = $m['title'];
			$module_pub = $m['pub'];
			$module_titlepub = $m['titlepub'];
			$module_enabled = $m['enabled'];
			$module_description = $m['description'];
			$quantity = $m['p1'];
			$length = $m['p2'];
			$module_ordering = $m['ordering'];
			$module_block =	$m['block'];
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
			<div class="container">
				<h1><img border="0" src="http://'.$site.'/modules/topnotes/admin/images/ico.png" width="25" height="25"  style="float: left; padding-top: 2px;" />&nbsp;&nbsp;Модуль "Популярные записи пользователей"</h1>

				<form method="POST" action="http://'.$site.'/admin/modules/topnotes/update/">
				<table class="main-tab">
					<tr>
						<td style="width:200px;">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Название модуля</td>
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
						<td>Опубликовать заголовок модуля</td>
						<td><input type="checkbox" name="titlepub" value="1" '.$titlepub.' ></td>
					</tr>
					<tr>
						<td>Позиция вывода, блок</td>
						<td>
							<select class="input" size="1" name="block">
							'.$block_option.'
							</select>
							&nbsp;- место вывода модуля
						</td>
					</tr>
					<tr>
						<td>Порядок следования</td>
						<td><input class="input" type="number" name="ordering" value="'.$module_ordering.'" class="width:80px;"></td>
					</tr>
					<tr>
						<td>Сколько выводить записей</td>
						<td><input class="input" type="number" name="quantity" value="'.$quantity.'" class="width:80px;"></td>
					</tr>
					<tr>
						<td>Лимит символов</td>
						<td><input class="input" type="number" name="length" value="'.$length.'" class="width:80px;"></td>
					</tr>
				</table>
<div style="margin:40px 0px 60px 0px"><input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none"></div>
				</form>
			</div>	
			';

		} // конец проверки 'enabled'
		else
		{
			echo '<div id="main-top">Модуль "topnotes" не подключён</div>';
		}
	} // конец функции
}

?>