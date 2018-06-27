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
	$query_update_newnotes = "UPDATE `modules` SET `title` = '$mod_title', `pub` = '$mod_pub', `titlepub` = '$mod_titlepub', `p1` = '$quantity', `p2` = '$length', `block` = '$mod_block', `ordering` = '$mod_ordering' WHERE `module` = 'newnotes' LIMIT 1 ;";

	$sql_newnotes = mysql_query($query_update_newnotes) or die ("Невозможно обновить данные");

	Header ("Location: http://".$site."/admin/modules/"); exit;
}
else {

	function a_com()
	{

	global $site, $quantity;

		// вывод содержимого модуля
		$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'newnotes'") or die ("Невозможно сделать выборку из таблицы - 1");

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
			<div id="main-top"><img border="0" src="http://'.$site.'/modules/newnotes/admin/images/ico.png" width="25" height="25"  style="float: left; padding-top: 2px;" />&nbsp;&nbsp;Модуль "Новые записи"</div>

			<div class="padding-horizontal-20">

				<form method="POST" action="http://'.$site.'/admin/modules/newnotes/update/">

				<table class="main-tab">
					<tr>
						<td width="200" height="25">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="200" height="25">Название модуля</td>
						<td><input type="text" name="title" size="20" value="'.$module_title.'"></td>
					</tr>
					<tr>
						<td width="200" height="25">Описание</td>
						<td>'.$module_description.'</td>
					</tr>
					<tr>
						<td width="200" height="25">Опубликовать модуль</td>
						<td><input type="checkbox" name="pub" value="1" '.$pub.' ></td>
					</tr>
					<tr>
						<td width="200" height="25">Опубликовать заголовок модуля</td>
						<td><input type="checkbox" name="titlepub" value="1" '.$titlepub.' ></td>
					</tr>
					<tr>
						<td width="200" height="25">Позиция вывода, блок</td>
						<td>
							<select size="1" name="block">
							'.$block_option.'
							</select>
							&nbsp;- место вывода модуля
						</td>
					</tr>
					<tr>
						<td width="200" height="25">Порядок следования</td>
						<td><input type="text" name="ordering" size="3" value="'.$module_ordering.'"></td>
					</tr>
					<tr>
						<td width="200" height="25">Сколько выводить записей</td>
						<td><input type="text" name="quantity" size="3" value="'.$quantity.'"></td>
					</tr>
					<tr>
						<td width="200" height="25">Лимит символов</td>
						<td><input type="text" name="length" size="3" value="'.$length.'"></td>
					</tr>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none"></div>
				</form>
			</div>
			';

		} // конец проверки 'enabled'
		else
		{
			echo '<div id="main-top">Модуль "newnotes" не подключён</div>';
		}
	} // конец функции
}

?>