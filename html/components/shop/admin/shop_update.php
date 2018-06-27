<?php
defined('AUTH') or die('Restricted access');

$title = htmlspecialchars($_POST["title"]);
$name = htmlspecialchars($_POST["menu"]);
$pub = intval($_POST["pub"]);
if(isset($_POST["parent"])){$parent = intval($_POST["parent"]);} else{$parent = 0;}
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$ordering = intval($_POST['ordering']);
$sef = checkingeditor($_POST["sef"]);
$text = $_POST["editor1"];

if(isset($_POST["name_id"])){$name_id_arr = $_POST["name_id"];} else{$name_id_arr = '';} // id названия характеристики
if(isset($_POST["value_1"])){$value_1_arr = $_POST["value_1"];} else{$value_1_arr = '';} // значение 1
if(isset($_POST["value_2"])){$value_2_arr = $_POST["value_2"];} else{$value_2_arr = '';} // значение 2

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/all"); exit;}

// Условие публикации
if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";}
if (!isset($parent) || $parent == ""){$parent = "0";}

// проверка заполнния пункта меню
if ($name == "" || $name == " ")
{
	$err = '<div id="main-top">Поле "Наименование пункта меню" не заплонено!</div>';
}
else {
	// находим "id_menu" и тип меню занесенный в базу
	$stmt_menu = $db->query("SELECT id, menu_type FROM menu WHERE component = 'shop' AND p1 = 'all';");

	while($mq = $stmt_menu->fetch())
	{
		$menu_id = $mq['id'];
		$menu_type_last = $mq['menu_type'];		
	}

	$shopSettings->shop_title = $title;
	$shopSettings->shop_text = $text;
	
	$shopSettings->tag_title = $tag_title;
	$shopSettings->tag_description = $tag_description;
	
	$s_serialize = serialize($shopSettings);

	$stmt = $db->prepare("UPDATE com_shop_settings SET parametr = :s_serialize WHERE name = 'settings'");
	$stmt->execute(array('s_serialize' => $s_serialize));

	$stmt_menu = $db->prepare("UPDATE menu SET menu_type = :menu_type, name = :name, pub = :pub, parent = :parent, ordering = :ordering, component = 'shop' WHERE component = 'shop' AND main = '1'");
	$stmt_menu->execute(array('menu_type' => $menu_type, 'name' => $name, 'pub' => $pub, 'parent' => $parent, 'ordering' => $ordering));
	
	// Добавляем характеристики
	if ($name_id_arr != '' && $value_1_arr != '')
	{
		$count = count($name_id_arr);
		for ($i = 0; $i < $count; $i++)
		{
			$stmt_filter = $db->prepare("SELECT id FROM com_shop_filter WHERE section_id = '0' AND char_id = :char_id");
			$stmt_filter->execute(array('char_id' => $name_id_arr[$i]));

			$filter_id = 0;
			while($m = $stmt_filter->fetch())
			{
				$filter_id = $m['id'];
			}

			if ($filter_id != 0) // Обновляем значения фильтра
			{
				$stmt_filter_update = $db->prepare("UPDATE com_shop_filter SET value_1 = :value_1, value_2 = :value_2, ordering = :ordering WHERE id = :filter_id LIMIT 1");
				$stmt_filter_update->execute(array('value_1' => $value_1_arr[$i], 'value_2' => $value_2_arr[$i], 'ordering' => $i, 'filter_id' => $filter_id));
			}
			else // Вставляем значения фильтра
			{
				if($name_id_arr[$i] != '')
				{
					// ищем тип характеристики (число / строка)
					$stmt_char = $db->prepare("SELECT id, type FROM com_shop_char_name WHERE id = :char_id");
					$stmt_char->execute(array('char_id' => intval($name_id_arr[$i])));

					while($m = $stmt_char->fetch())
					{
						$char_id = $m['id'];
						$type = $m['type'];
					}

					// вставляем значения
					if ($type == 'number')
					{
						$value_1_arr[$i] = str_replace(',', '.', $value_1_arr[$i]);
						$value_1 = (float)$value_1_arr[$i];

						$value_2_arr[$i] = str_replace(',', '.', $value_2_arr[$i]);
						$value_2 = (float)$value_2_arr[$i];
						
						$stmt_insert = $db->prepare("INSERT INTO com_shop_filter SET id = NULL, section_id = 0, char_id = :char_id, value_1 = :value_1, value_2 = :value_2, ordering = :ordering");
						$stmt_insert->execute(array('char_id' => $char_id, 'value_1' => $value_1, 'value_2' => $value_2, 'ordering' => $i));
					}

					if ($type == 'string')
					{
						$s = array("'", '"');	// заменить кавычки
						$value = trim(str_replace($s, '`', $value_1_arr[$i]));
						$pattern = "/[^(a-z0-9а-яё\_\-\+\.\;\ \(\)\`\/\\)]/iu";
						$replacement = "";
						$value = preg_replace($pattern, $replacement, $value);

						$stmt_insert = $db->prepare("INSERT INTO com_shop_filter SET id = NULL, section_id = 0, char_id = :char_id, value_1 = :value_1, value_2 = '', ordering = :ordering");
						$stmt_insert->execute(array('char_id' => $char_id, 'value_1' => $value, 'ordering' => $i));
					}
				}
			}
		}
	}

	// --- ЧПУ URL ----------------------------------------------------------------------------------------
	if(isset($sef))
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
				$stmt_sef = $db->prepare("SELECT id FROM url WHERE sef = :sef AND url <> 'shop/all/1'");
				$stmt_sef->execute(array('sef' => $sef));

				if($stmt_sef->rowCount() == 0) // нет такого `sef` (наш не в счёт)
				{
					// проверяем - есть ли уже запись
					$stmt_url = $db->query("SELECT id FROM url WHERE url = 'shop/all/1'");				

					if($stmt_url->rowCount() > 0) // запись есть
					{
						// Обновляем данные в таблице "url"
						$stmt_update = $db->prepare("UPDATE url SET sef = :sef WHERE url = 'shop/all/1' LIMIT 1");
						$stmt_update->execute(array('sef' => $sef));
					}
					else // запись отсутствует
					{
						// Вставляем в таблицу
						$stmt_insert = $db->prepare("INSERT INTO url SET url = 'shop/all/1', sef = :sef");
						$stmt_insert->execute(array('sef' => $sef));
					}
				}
			}
		}

		if($sef == '')
		{
			$stmt_delete = $db->query("DELETE FROM url WHERE url = 'shop/all/1'");
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------


	// --- Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---
	if ($menu_type != $menu_type_last)
	{
		// обновляем не только все пункты, но и подпункты данного меню
		tree($menu_type, $menu_id, 0);
	}
	// --- / Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/shop/shopedit/1/2"); exit;}

} // конец условия заполненного пункта меню



########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $menu_id, $lvl) // $menu_type 1 - верхнее 2 - левое  $page_id = 0 начальный уровень меню, $lvl - уровень меню
{
	global $db;
	
	$stmt_select = $db->prepare("SELECT id, name, id_com FROM menu WHERE parent = :menu_id ORDER BY ordering ASC");
	$stmt_select->execute(array('menu_id' => $menu_id));

	if ($stmt_select->rowCount() > 0)
	{
		while($m = $stmt_select->fetch())
		{
			$menu_id = $m['id'];
			$menu_name = $m['name'];
			$menu_id_com = $m['id_com'];

			// Обновляем данные в таблице "menu"
			$stmt_update = $db->prepare("UPDATE menu SET menu_type = :menu_type WHERE id = :menu_id");
			$stmt_update->execute(array('menu_type' => $menu_type, 'menu_id' => $menu_id));

			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu_id, $lvl);			
		}
	} // конец проверки $result > 0
} // конец функции tree

// ==================================================================================

function a_com()
{
	global $err;

	echo $err;

} // конец функции

?>
