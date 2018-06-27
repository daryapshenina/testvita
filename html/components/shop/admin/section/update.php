<?php
defined('AUTH') or die('Restricted access');

$section_id = intval($_POST["section_id"]);
$section_title = trim(htmlspecialchars($_POST["title"]));
if(isset($_POST["sectionpub"])){$section_pub = intval($_POST["sectionpub"]);} else{$section_pub = 0;}
$menu_name = trim(htmlspecialchars($_POST["menuname"]));
if(isset($_POST["menupub"])){$menu_pub = intval($_POST["menupub"]);} else{$menu_pub = 0;}
if(isset($_POST["parent"])){$menu_parent = intval($_POST["parent"]);} else{$menu_parent = 0;}
$menu_ordering = intval($_POST["menuordering"]);
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
$sef = checkingeditor($_POST["sef"]);

if(isset($_POST["name_id"])){$name_id_arr = $_POST["name_id"];} else{$name_id_arr = '';} // id названия характеристики
if(isset($_POST["value_1"])){$value_1_arr = $_POST["value_1"];} else{$value_1_arr = '';} // значение 1
if(isset($_POST["value_2"])){$value_2_arr = $_POST["value_2"];} else{$value_2_arr = '';} // значение 2

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

$section_description = $_POST["editor1"];

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin"); exit;}

if (!isset($section_pub) || $section_pub == ""){$s_pub = "0";} else{$s_pub = "1";} // Условие публикации раздела
if ($menu_pub == "" || $section_pub == 0){$m_pub = "0";} else{$m_pub = "1";} // Условие публикации пункта меню

if ($section_title == "" || $section_title == " " || $menu_name == "" || $menu_name == " " )
{
	$err = '<div id="main-top">Поле "Название раздела" или "Пункт меню" не заплонено!</div>';
}
else 
{
	// находим "id_menu" и тип меню занесенный в базу
	$stmt_menu = $db->prepare("SELECT id, menu_type FROM menu WHERE component = 'shop' AND p1 = 'section' AND id_com = :id_com");
	$stmt_menu->execute(array('id_com' => $section_id));
	
	$menu = $stmt_menu->fetch();
	
	$stmt_update = $db->prepare("
		UPDATE menu SET
		menu_type = :menu_type,
		name = :name,
		pub = :pub,
		parent = :parent,
		ordering = :ordering
		WHERE id_com = :id_com AND component = 'shop' AND p1 = 'section' LIMIT 1
	");
	
	$stmt_update->execute(array(
		'menu_type' => $menu_type,
		'name' => $menu_name,
		'pub' => $m_pub,
		'parent' => $menu_parent,
		'ordering' => $menu_ordering,
		'id_com' => $section_id
	));

	// Обновляем данные в таблице "com_shop_section"
	$stmt_section = $db->prepare("
		UPDATE com_shop_section SET
		pub = :pub,
		title = :title,
		description = :description,
		tag_title = :tag_title,
		tag_description = :tag_description,
		date = NOW()
		WHERE id = :id
	");
	
	$stmt_section->execute(array(
		'pub' => $s_pub,
		'title' => $section_title,
		'description' => $section_description,
		'tag_title' => $tag_title,
		'tag_description' => $tag_description,
		'id' => $section_id
	));

	// --- Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---
	if ($menu_type != $menu['menu_type'])
	{
		// обновляем не только все пункты, но и подпункты данного меню
		tree($menu_type, $menu['id'], 0);
	}
	// --- / Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---


	// Добавляем характеристики
	if ($name_id_arr != '' && $value_1_arr != '')
	{
		$count = count($name_id_arr);
		for ($i = 0; $i < $count; $i++)
		{
			// Ищем - есть ли уже данный фильтр
			$stmt_filter_select = $db->prepare("SELECT id FROM com_shop_filter WHERE section_id = :section_id AND char_id = :char_id LIMIT 1");
			$stmt_filter_select->execute(array('section_id' => $section_id, 'char_id' => $name_id_arr[$i]));

			$filter_id = $stmt_filter_select->fetchColumn();


			if ($filter_id != 0) // Обновляем значения фильтра
			{
				$stmt_filter_update = $db->prepare("UPDATE com_shop_filter SET value_1 = :value_1, value_2 = :value_2, ordering = :ordering WHERE id = :id");
				$stmt_filter_update->execute(array('value_1' => $value_1_arr[$i], 'value_2' => $value_2_arr[$i], 'ordering' => $i, 'id' => $filter_id));
			}
				else // Вставляем значения фильтра
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
				if (!preg_match("/^[a-z0-9-_\/]{1,255}$/is",$sef))
				{
					$sef_err = 1;
				}
				else
				{
					$sef = strtolower($sef); // в нижний регистр

					// проверяем - есть ли уже запись для `sef`
					$stmt_sef = $db->prepare("SELECT id FROM url WHERE sef = :sef AND url <> :url");
					$stmt_sef->execute(array('sef' => $sef, 'url' => 'shop/section/'.$section_id));

					if($stmt_sef->rowCount() == 0) // нет такого `sef` (наш не в счёт)
					{
						// проверяем - есть ли уже запись
						$stmt_url = $db->prepare("SELECT id FROM url WHERE url = :url");
						$stmt_url->execute(array('url' => 'shop/section/'.$section_id));

						if($stmt_url->rowCount() > 0) // запись есть
						{
							// Обновляем данные в таблице "url"
							$stmt_update = $db->prepare("UPDATE url SET sef = :sef WHERE url = :url LIMIT 1");
							$stmt_update->execute(array('sef' => $sef, 'url' => 'shop/section/'.$section_id));
						}
						else // запись отсутствует
						{
							// Вставляем данные в таблице "url"
							$stmt_insert = $db->prepare("INSERT INTO url SET url = :url, sef = :sef");
							$stmt_insert->execute(array('url' => 'shop/section/'.$section_id, 'sef' => $sef));
						}
					}
				}
			}

			if($sef == '')
			{
				// Обновляем данные в таблице "url"
				$stmt_delete = $db->prepare("DELETE FROM url WHERE url = :url");
				$stmt_delete->execute(array('url' => 'shop/section/'.$section_id));
			}
		}
		// --- / ЧПУ URL / -------------------------------------------------------------------------------------


		if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
		else {Header ("Location: /admin/com/shop/section/edit/".$section_id); exit;}


} // конец условия заполненного пункта меню



########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $menu_id, $lvl) // $menu_type 1 - верхнее 2 - левое  $page_id = 0 начальный уровень меню, $lvl - уровень меню
{
	global $db;

	$stmt_menu = $db->prepare("SELECT id FROM menu WHERE parent = :parent ORDER BY ordering ASC");
	$stmt_menu->execute(array('parent' => $menu_id));

	if ($stmt_menu->rowCount() > 0)
	{
		while($menu = $stmt_menu->fetch())
		{
			// Обновляем данные в таблице "menu"
			$stmt_update = $db->prepare("UPDATE menu SET menu_type = :menu_type WHERE id = :id");
			$stmt_update->execute(array('menu_type' => $menu_type, 'id' => $menu['id']));

			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu['id'], $lvl);			
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
