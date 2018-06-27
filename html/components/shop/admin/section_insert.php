<?php
// DAN обновление - январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$section_title = htmlspecialchars($_POST["title"]);
$section_pub = intval($_POST["sectionpub"]);
$menu_name = htmlspecialchars($_POST["menuname"]);
$menu_pub = intval($_POST["menupub"]);
$menu_t = $_POST["menu_type"];
$menu_parent = intval($_POST["parent"]);
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$sef = checkingeditor($_POST["sef"]);

for($i = 1; $i <= 30; $i++)
{
	$char_enable[$i] = intval($_POST["char_enable_".$i]);
	$characteristic[$i] = htmlspecialchars($_POST["characteristic_".$i]);
	$char_unit[$i] = htmlspecialchars($_POST["char_unit_".$i]);
	$filter_enable[$i] = intval($_POST["filter_enable_".$i]);

	if($i < 26)
	{
		$filter[$i] = htmlspecialchars($_POST["filter_".$i]);
	}
	else
	{
		$filter[$i] =  floatval($_POST['filter_ot_'.$i]).';'.floatval($_POST['filter_do_'.$i]);
	}
}

$bt_save = $_POST["bt_save"]; // кнопка 'Сохранить'
$bt_prim = $_POST["bt_prim"]; // кнопка 'Применить'
$bt_none = $_POST["bt_none"]; // кнопка 'Отменить'

// Получаем характеристики
$specs = '';
for($i = 1;$i < 31;$i++)
{
	$specs .= intval($_POST["specs_".$i]);
}

$section_description = $_POST["editor1"];
$none = $_POST["none"]; // кнопка 'Отменить'

// № пункта преобразуем в число
$menu_id = intval($admin_d4);

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){	Header ("Location: http://".$site."/admin/com"); exit;}

// условие заполненного заголовка
if ($section_title == "" || $section_title == " " || $menu_name == "" || $menu_name == " ")
{
	$err .= '<div id="main-top">Поле "Наименование раздела" или "Наименование пункта меню" не заплонено!</div>';
}

else {
	// ======= Вставляем данные в таблицу меню =====================================================
	// Находим максимальное значение поля ordering для главного пункта интернет - магазина
	$mo = "SELECT max(ordering) `ordering` FROM `menu` WHERE  `menu_type` = '$menu_type' AND `component`='shop'";
	$mxo = mysql_query($mo);
	while($maxord = mysql_fetch_array($mxo)):
		$maxordering = $maxord['ordering'];
	endwhile;

	// устанавливаем порядок для нового пункта на 1 больше последнего
	$ordering = $maxordering + 1;

	// Находим все пункты меню, следующие за этим
	$n_sql_query = "SELECT * FROM `menu` WHERE  `menu_type` = '$menu_type' AND `ordering`>='$ordering'";
	$n_sql = mysql_query($n_sql_query) or die ("Невозможно выбрать данные 2");
	while($n = mysql_fetch_array($n_sql)):
		$menu_id = $n['id'];
		$menu_ordering = $n['ordering'];

		$menu_ordering = $menu_ordering + 1;

		// Обновляем данные в таблице "menu" для пунктов с порядком на единицу большим нашего
		$query_update_menu = "UPDATE `menu` SET `ordering` = '$menu_ordering' WHERE `menu_type` = '$menu_type' AND `id` = '$menu_id';";
		$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 3");
	endwhile;

	// ======= Вставляем данные в таблицу разделов интернет - магазина ==============================
	// Находим максимальное значение поля ordering для этого родительского уровня меню
	$mo = "SELECT max(ordering) ordering FROM com_shop_section";
	$mxo = mysql_query($mo);
	while($maxord = mysql_fetch_array($mxo)):
		$maxordering = $maxord['ordering'];
	endwhile;

	$maxordering++;

	// Вставляем в таблицу "com_shop_section"
	$section_insert_query = "INSERT INTO `com_shop_section` (`id`, `identifier`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, `char_enable_1`, `char_enable_2`, `char_enable_3`, `char_enable_4`, `char_enable_5`, `char_enable_6`, `char_enable_7`, `char_enable_8`, `char_enable_9`, `char_enable_10`, `char_enable_11`, `char_enable_12`, `char_enable_13`, `char_enable_14`, `char_enable_15`, `char_enable_16`, `char_enable_17`, `char_enable_18`, `char_enable_19`, `char_enable_20`, `char_enable_21`, `char_enable_22`, `char_enable_23`, `char_enable_24`, `char_enable_25`, `char_enable_26`, `char_enable_27`, `char_enable_28`, `char_enable_29`, `char_enable_30`, `characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`, `characteristic_11`, `characteristic_12`, `characteristic_13`, `characteristic_14`, `characteristic_15`, `characteristic_16`, `characteristic_17`, `characteristic_18`, `characteristic_19`, `characteristic_20`, `characteristic_21`, `characteristic_22`, `characteristic_23`, `characteristic_24`, `characteristic_25`, `characteristic_26`, `characteristic_27`, `characteristic_28`, `characteristic_29`, `characteristic_30`, `char_unit_1`, `char_unit_2`, `char_unit_3`, `char_unit_4`, `char_unit_5`, `char_unit_6`, `char_unit_7`, `char_unit_8`, `char_unit_9`, `char_unit_10`, `char_unit_11`, `char_unit_12`, `char_unit_13`, `char_unit_14`, `char_unit_15`, `char_unit_16`, `char_unit_17`, `char_unit_18`, `char_unit_19`, `char_unit_20`, `char_unit_21`, `char_unit_22`, `char_unit_23`, `char_unit_24`, `char_unit_25`, `char_unit_26`, `char_unit_27`, `char_unit_28`, `char_unit_29`, `char_unit_30`, `filter_enable_1`, `filter_enable_2`, `filter_enable_3`, `filter_enable_4`, `filter_enable_5`, `filter_enable_6`, `filter_enable_7`, `filter_enable_8`, `filter_enable_9`, `filter_enable_10`, `filter_enable_11`, `filter_enable_12`, `filter_enable_13`, `filter_enable_14`, `filter_enable_15`, `filter_enable_16`, `filter_enable_17`, `filter_enable_18`, `filter_enable_19`, `filter_enable_20`, `filter_enable_21`, `filter_enable_22`, `filter_enable_23`, `filter_enable_24`, `filter_enable_25`, `filter_enable_26`, `filter_enable_27`, `filter_enable_28`, `filter_enable_29`, `filter_enable_30`, `filter_1`, `filter_2`, `filter_3`, `filter_4`, `filter_5`, `filter_6`, `filter_7`, `filter_8`, `filter_9`, `filter_10`, `filter_11`, `filter_12`, `filter_13`, `filter_14`, `filter_15`, `filter_16`, `filter_17`, `filter_18`, `filter_19`, `filter_20`, `filter_21`, `filter_22`, `filter_23`, `filter_24`, `filter_25`, `filter_26`, `filter_27`, `filter_28`, `filter_29`, `filter_30`, `date`) VALUES (NULL, '', '$section_pub', '0', '$maxordering', '$section_title', '$section_description', '$tag_title', '$tag_description', '$char_enable[1]', '$char_enable[2]', '$char_enable[3]', '$char_enable[4]', '$char_enable[5]', '$char_enable[6]', '$char_enable[7]', '$char_enable[8]', '$char_enable[9]', '$char_enable[10]', '$char_enable[11]', '$char_enable[12]', '$char_enable[13]', '$char_enable[14]', '$char_enable[15]', '$char_enable[16]', '$char_enable[17]', '$char_enable[18]', '$char_enable[19]', '$char_enable[20]', '$char_enable[21]', '$char_enable[22]', '$char_enable[23]', '$char_enable[24]', '$char_enable[25]', '$char_enable[26]', '$char_enable[27]', '$char_enable[28]', '$char_enable[29]', '$char_enable[30]', '$characteristic[1]', '$characteristic[2]', '$characteristic[3]', '$characteristic[4]', '$characteristic[5]', '$characteristic[6]', '$characteristic[7]', '$characteristic[8]', '$characteristic[9]', '$characteristic[10]', '$characteristic[11]', '$characteristic[12]', '$characteristic[13]', '$characteristic[14]', '$characteristic[15]', '$characteristic[16]', '$characteristic[17]', '$characteristic[18]', '$characteristic[19]', '$characteristic[20]', '$characteristic[21]', '$characteristic[22]', '$characteristic[23]', '$characteristic[24]', '$characteristic[25]', '$characteristic[26]', '$characteristic[27]', '$characteristic[28]', '$characteristic[29]', '$characteristic[30]', '$char_unit[1]', '$char_unit[2]', '$char_unit[3]', '$char_unit[4]', '$char_unit[5]', '$char_unit[6]', '$char_unit[7]', '$char_unit[8]', '$char_unit[9]', '$char_unit[10]', '$char_unit[11]', '$char_unit[12]', '$char_unit[13]', '$char_unit[14]', '$char_unit[15]', '$char_unit[16]', '$char_unit[17]', '$char_unit[18]', '$char_unit[19]', '$char_unit[20]', '$char_unit[21]', '$char_unit[22]', '$char_unit[23]', '$char_unit[24]', '$char_unit[25]', '$char_unit[26]', '$char_unit[27]', '$char_unit[28]', '$char_unit[29]', '$char_unit[30]', '$filter_enable[1]', '$filter_enable[2]', '$filter_enable[3]', '$filter_enable[4]', '$filter_enable[5]', '$filter_enable[6]', '$filter_enable[7]', '$filter_enable[8]', '$filter_enable[9]', '$filter_enable[10]', '$filter_enable[11]', '$filter_enable[12]', '$filter_enable[13]', '$filter_enable[14]', '$filter_enable[15]', '$filter_enable[16]', '$filter_enable[17]', '$filter_enable[18]', '$filter_enable[19]', '$filter_enable[20]', '$filter_enable[21]', '$filter_enable[22]', '$filter_enable[23]', '$filter_enable[24]', '$filter_enable[25]', '$filter_enable[26]', '$filter_enable[27]', '$filter_enable[28]', '$filter_enable[29]', '$filter_enable[30]', '$filter[1]', '$filter[2]', '$filter[3]', '$filter[4]', '$filter[5]', '$filter[6]', '$filter[7]', '$filter[8]', '$filter[9]', '$filter[10]', '$filter[11]', '$filter[12]', '$filter[13]', '$filter[14]', '$filter[15]', '$filter[16]', '$filter[17]', '$filter[18]', '$filter[19]', '$filter[20]', '$filter[21]', '$filter[22]', '$filter[23]', '$filter[24]', '$filter[25]', '$filter[26]', '$filter[27]', '$filter[28]', '$filter[29]', '$filter[30]', NOW())";

	$section_insert_sql = mysql_query($section_insert_query) or die("Ошибка: $section_insert_query");

	$id_com = mysql_insert_id();

	// ======= Вставляем данны в таблицу меню ===========================================================
	// Вставляем новый пункт в таблицу меню
	$query_insert_menu = "INSERT INTO `menu` (menu_type, name, description, pub, parent, ordering, component, main, p1, p2, p3, id_com, prefix_css) VALUES('$menu_type', '$menu_name', 'раздел интернет-магазина', '$menu_pub', '$menu_parent', '$ordering', 'shop', '0', 'section', '', '', '$id_com', '')";

	$sql_menu = mysql_query($query_insert_menu) or die ("Невозможно вставить данные 1");



	// --- ЧПУ URL ----------------------------------------------------------------------------------------
	if(isset($sef) && $sef != '')
	{
		if(classvalidation::checkReservedWord($sef)) // зарезервированно
		{
			$sef_err = 1;
		}
		else
		{
			// проверяем на символы
			if (!preg_match("/^[a-z0-9-\/]{1,255}$/is",$sef))
			{
				$sef_err = 1;
			}
			else
			{
				$sef = strtolower($sef); // в нижний регистр

				// проверяем - есть ли уже запись для `sef`
				$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef'") or die ("Ошибка - 1");
				$sef_query_result = mysql_num_rows($sef_query);

				if($sef_query_result == 0) // нет такого `sef`
				{
					// Вставляем данные в таблице "url"
					$url_sql = "INSERT INTO `url` (url, sef) VALUES('shop/section/$id_com', '$sef')";
					$url_query = mysql_query($url_sql) or die ("Невозможно вставить данные 4");
				}
			}
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: http://".$site."/admin"); exit;}
	else {Header ("Location: http://".$site."/admin/com/shop/sectionedit/".$id_com); exit;}

}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
