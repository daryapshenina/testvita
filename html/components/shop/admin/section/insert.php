<?php
defined('AUTH') or die('Restricted access');

$title = trim(htmlspecialchars($_POST["title"]));
if(isset($_POST["sectionpub"])){$section_pub = intval($_POST["sectionpub"]);} else {$section_pub = 0;}
$menu_name = trim(htmlspecialchars($_POST["menuname"]));
if(isset($_POST["menupub"])){$menu_pub = intval($_POST["menupub"]);} else {$menu_pub = 0;}
$menu_t = $_POST["menu_type"];
if(isset($_POST["parent"])){$menu_parent = intval($_POST["parent"]);} else {$menu_parent = 0;}
$ordering = intval($_POST['menu_ordering']);
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
$sef = checkingeditor($_POST["sef"]);

if(isset($_POST["name_id"])){$name_id_arr = $_POST["name_id"];} else{$name_id_arr = '';} // название переменной
if(isset($_POST["value_1"])){$value_1_arr = $_POST["value_1"];} else{$value_1_arr = '';} // значение 1
if(isset($_POST["value_2"])){$value_2_arr = $_POST["value_2"];} else{$value_2_arr = '';} // значение 2

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

$section_description = $_POST["editor1"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com"); exit;}

// условие заполненного заголовка
if ($title == "" || $title == " " || $menu_name == "" || $menu_name == " ")
{
	$err .= '<div id="main-top">Поле "Наименование раздела" или "Наименование пункта меню" не заплонено!</div>';
}
else {
	// ======= Вставляем данные в таблицу меню =====================================================
	
	// Вставляем в таблицу "com_shop_section"
	$stmt_section_insert = $db->prepare("
		INSERT INTO com_shop_section SET
		identifier = '',
		pub = :pub,
		parent = '0',
		ordering = '0',
		title = :title,
		description = :description,
		tag_title = :tag_title,
		tag_description = :tag_description,
		date = NOW();
	");

	$stmt_section_insert->execute(array(
		'pub' => $section_pub,
		'title' => $title,
		'description' => $section_description,
		'tag_title' => $tag_title,
		'tag_description' => $tag_description
	));

	$section_id = $db->lastInsertId();


	// Вставляем новый пункт в таблицу меню
	$stmt_menu_insert = $db->prepare("
		INSERT INTO menu SET
		menu_type = :menu_type,
		name = :menu_name,
		description = 'раздел интернет-магазина',
		pub = :pub,
		parent = :parent,
		ordering = :ordering,
		component = 'shop',
		main = '0',
		p1 = 'section',
		p2 = '',
		p3 = '',
		id_com = :id_com,
		prefix_css = ''
	");

	$stmt_menu_insert->execute(array(
		'menu_type' => $menu_type,
		'menu_name' => $menu_name,
		'pub' => $menu_pub,
		'parent' => $menu_parent,
		'ordering' => $ordering,
		'id_com' => $section_id
	));

	$menu_id = $db->lastInsertId();


	$stmt_menu = $db->prepare("SELECT id, ordering FROM menu WHERE parent = :parent ORDER BY ordering ASC");
	$stmt_menu->execute(array('parent' => $menu_parent));

	$ordering_arr = $stmt_menu->fetchAll();

	// Считаем ogdering по порядку, т.к. могут быть разрывы 1,2,3, 8,9
	$count = count($ordering_arr);

	for ($i = 0; $i < $count; $i++)
	{
		$ordering_arr[$i]['ordering'] = $i;
		if($ordering_arr[$i]['id'] == $menu_id){$i_this = $i;}
	}

	// Если это не последний элемент - меняем местами значения ячеек
	if($i_this < $count - 1)
	{
		$ordering_arr[($i_this - 1)]['ordering'] = $i_this;  // следующая ячейка = текущему значению ячейки 'ordering' = $i_this
		$ordering_arr[($i_this)]['ordering'] = $i_this + 1;
	}


	// Записываем значения в базу данных
	for ($i = 0; $i < $count; $i++)
	{
		$id = $ordering_arr[$i]['id'];
		$ordering = $ordering_arr[$i]['ordering'];

		$stmt_update = $db->prepare("UPDATE menu SET ordering = :ordering WHERE id = :id");
		$stmt_update->execute(array('ordering' => $ordering, 'id' => $id));
	}


	// Характеристики
	if ($name_id_arr != '' && $value_1_arr != '')
	{
		$count = count($name_id_arr);
		for ($i = 0; $i < $count; $i++)
		{
			if($name_id_arr[$i] != '')
			{
				// ищем тип характеристики (число / строка)
				$stmt_char = $db->prepare("SELECT id, type FROM com_shop_char_name WHERE id = :id");
				$stmt_char->execute(array('id' => $name_id_arr[$i]));

				$char = $stmt_char->fetch();

				// вставляем значения
				if ($char['type'] == 'number')
				{
					$value_1_arr[$i] = str_replace(',', '.', $value_1_arr[$i]);
					$value_1 = (float)$value_1_arr[$i];

					$value_2_arr[$i] = str_replace(',', '.', $value_2_arr[$i]);
					$value_2 = (float)$value_2_arr[$i];

					$stmt_insert = $db->prepare("
						INSERT INTO com_shop_filter SET
						section_id = :section_id,
						char_id = :char_id,
						value_1 = :value_1,
						value_2 = :value_2,
						ordering = :ordering
					");

					$stmt_insert->execute(array(
						'section_id' => $section_id,
						'char_id' => $char['id'],
						'value_1' => $value_1,
						'value_2' => $value_2,
						'ordering' => $i,
					));
				}

				if ($char['type'] == 'string')
				{
					$s = array("'", '"');	// заменить кавычки
					$value = trim(str_replace($s, '`', $value_1_arr[$i]));
					$pattern = "/[^(a-z0-9а-яё\_\-\+\.\;\ \(\)\`\/\\)]/iu";
					$replacement = "";
					$value = preg_replace($pattern, $replacement, $value);


					$stmt_insert = $db->prepare("
						INSERT INTO com_shop_filter SET
						section_id = :section_id,
						char_id = :char_id,
						value_1 = :value_1,
						value_2 = '',
						ordering = :ordering
					");
					
					$stmt_insert->execute(array(
						'section_id' => $section_id,
						'char_id' => $char['id'],
						'value_1' => $value,
						'ordering' => $i,
					));
				}
			}
		}
	}



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
			if (!preg_match("/^[a-z0-9-_\/]{1,255}$/is",$sef))
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
					// Вставляем в таблицу
					$stmt_insert = $db->prepare("INSERT INTO url SET url = :url, sef = :sef");
					$stmt_insert->execute(array('url' => 'shop/section/'.$section_id, 'sef' => $sef));
				}
			}
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/shop/section/edit/".$section_id); exit;}

}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
