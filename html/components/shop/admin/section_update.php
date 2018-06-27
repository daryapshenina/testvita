<?php
// DAN обновлено январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

$section_id = intval($_POST["section_id"]);
$section_title = htmlspecialchars($_POST["title"]);
$section_pub = intval($_POST["sectionpub"]);
$menu_name = htmlspecialchars($_POST["menuname"]);
$menu_pub = intval($_POST["menupub"]);
$menu_parent = intval($_POST["parent"]);
$menu_ordering = intval($_POST["menuordering"]);
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

// определяем тип мею
$menu_t = $_POST["menu_type"];

// оределяем - какое меню надо редактировать и какую таблицу подключать
if ($menu_t == "menu_top"){$menu_type = "top";}
elseif ($menu_t == "menu_left"){$menu_type = "left";}
else {$menu_type = "left";}

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: http://".$site."/admin"); exit;} 

if (!isset($section_pub) || $section_pub == ""){$s_pub = "0";} else{$s_pub = "1";} // Условие публикации раздела
if (!isset($menu_pub) || $menu_pub == "" || $section_pub == 0){$m_pub = "0";} else{$m_pub = "1";} // Условие публикации пункта меню

if ($section_title == "" || $section_title == " " || $menu_name == "" || $menu_name == " " ) 
{ 
	$err = '<div id="main-top">Поле "Название раздела" или "Пункт меню" не заплонено!</div>';
}
else {
		// находим "id_menu" и тип меню занесенный в базу
		$id_menu_query = mysql_query("SELECT * FROM `menu` WHERE `component` = 'shop' AND `p1` = 'section' AND `id_com` = '$section_id';") or die ("Невозможно сделать выборку из таблицы - 1");
		
		while($mq = mysql_fetch_array($id_menu_query )):
			$menu_id = $mq['id'];	
			$menu_type_last = $mq['menu_type'];	
		endwhile;

	// Обновляем данные в таблице "menu"	
		$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type', `name` = '$menu_name', `pub` = '$m_pub', `parent` = '$menu_parent', `ordering` = '$menu_ordering' WHERE `id_com` = '$section_id' AND `component` = 'shop' AND `p1` = 'section' LIMIT 1" ;
		
		$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 2");	
	
	// Обновляем данные в таблице "com_shop_section"	
		$section_update_query = "UPDATE `com_shop_section` SET  `pub` = '$s_pub', `title` = '$section_title', `description` = '$section_description', `tag_title` = '$tag_title', `tag_description` = '$tag_description', 

		`char_enable_1` = '$char_enable[1]', `char_enable_2` = '$char_enable[2]', `char_enable_3` = '$char_enable[3]', `char_enable_4` = '$char_enable[4]', `char_enable_5` = '$char_enable[5]', 
		`char_enable_6` = '$char_enable[6]', `char_enable_7` = '$char_enable[7]', `char_enable_8` = '$char_enable[8]', `char_enable_9` = '$char_enable[9]', `char_enable_10` = '$char_enable[10]', `char_enable_11` = '$char_enable[11]', `char_enable_12` = '$char_enable[12]', `char_enable_13` = '$char_enable[13]', `char_enable_14` = '$char_enable[14]', `char_enable_15` = '$char_enable[15]', `char_enable_16` = '$char_enable[16]', `char_enable_17` = '$char_enable[17]', `char_enable_18` = '$char_enable[18]', `char_enable_19` = '$char_enable[19]', `char_enable_20` = '$char_enable[20]', `char_enable_21` = '$char_enable[21]', `char_enable_22` = '$char_enable[22]', `char_enable_23` = '$char_enable[23]', `char_enable_24` = '$char_enable[24]', `char_enable_25` = '$char_enable[25]', `char_enable_26` = '$char_enable[26]', `char_enable_27` = '$char_enable[27]', `char_enable_28` = '$char_enable[28]', `char_enable_29` = '$char_enable[29]', `char_enable_30` = '$char_enable[30]',
		`characteristic_1` = '$characteristic[1]', `characteristic_2` = '$characteristic[2]', `characteristic_3` = '$characteristic[3]', `characteristic_4` = '$characteristic[4]', `characteristic_5` = '$characteristic[5]',
		`characteristic_6` = '$characteristic[6]', `characteristic_7` = '$characteristic[7]', `characteristic_8` = '$characteristic[8]', `characteristic_9` = '$characteristic[9]', `characteristic_10` = '$characteristic[10]', `characteristic_11` = '$characteristic[11]', `characteristic_12` = '$characteristic[12]', `characteristic_13` = '$characteristic[13]', `characteristic_14` = '$characteristic[14]', `characteristic_15` = '$characteristic[15]', `characteristic_16` = '$characteristic[16]', `characteristic_17` = '$characteristic[17]', `characteristic_18` = '$characteristic[18]', `characteristic_19` = '$characteristic[19]', `characteristic_20` = '$characteristic[20]', `characteristic_21` = '$characteristic[21]', `characteristic_22` = '$characteristic[22]', `characteristic_23` = '$characteristic[23]', `characteristic_24` = '$characteristic[24]', `characteristic_25` = '$characteristic[25]', `characteristic_26` = '$characteristic[26]', `characteristic_27` = '$characteristic[27]', `characteristic_28` = '$characteristic[28]', `characteristic_29` = '$characteristic[29]', `characteristic_30` = '$characteristic[30]',
		`char_unit_1` = '$char_unit[1]', `char_unit_2` = '$char_unit[2]', `char_unit_3` = '$char_unit[3]', `char_unit_4` = '$char_unit[4]', `char_unit_5` = '$char_unit[5]', 
		`char_unit_6` = '$char_unit[6]', `char_unit_7` = '$char_unit[7]', `char_unit_8` = '$char_unit[8]', `char_unit_9` = '$char_unit[9]', `char_unit_10` = '$char_unit[10]', `char_unit_11` = '$char_unit[11]', `char_unit_12` = '$char_unit[12]', `char_unit_13` = '$char_unit[13]', `char_unit_14` = '$char_unit[14]', `char_unit_15` = '$char_unit[15]', `char_unit_16` = '$char_unit[16]', `char_unit_17` = '$char_unit[17]', `char_unit_18` = '$char_unit[18]', `char_unit_19` = '$char_unit[19]', `char_unit_20` = '$char_unit[20]', `char_unit_21` = '$char_unit[21]', `char_unit_22` = '$char_unit[22]', `char_unit_23` = '$char_unit[23]', `char_unit_24` = '$char_unit[24]', `char_unit_25` = '$char_unit[25]', `char_unit_26` = '$char_unit[26]', `char_unit_27` = '$char_unit[27]', `char_unit_28` = '$char_unit[28]', `char_unit_29` = '$char_unit[29]', `char_unit_30` = '$char_unit[30]',
		`filter_enable_1` = '$filter_enable[1]', `filter_enable_2` = '$filter_enable[2]', `filter_enable_3` = '$filter_enable[3]', `filter_enable_4` = '$filter_enable[4]', `filter_enable_5` = '$filter_enable[5]', 
		`filter_enable_6` = '$filter_enable[6]', `filter_enable_7` = '$filter_enable[7]', `filter_enable_8` = '$filter_enable[8]', `filter_enable_9` = '$filter_enable[9]', `filter_enable_10` = '$filter_enable[10]', `filter_enable_11` = '$filter_enable[11]', `filter_enable_12` = '$filter_enable[12]', `filter_enable_13` = '$filter_enable[13]', `filter_enable_14` = '$filter_enable[14]', `filter_enable_15` = '$filter_enable[15]', `filter_enable_16` = '$filter_enable[16]', `filter_enable_17` = '$filter_enable[17]', `filter_enable_18` = '$filter_enable[18]', `filter_enable_19` = '$filter_enable[19]', `filter_enable_20` = '$filter_enable[20]', `filter_enable_21` = '$filter_enable[21]', `filter_enable_22` = '$filter_enable[22]', `filter_enable_23` = '$filter_enable[23]', `filter_enable_24` = '$filter_enable[24]', `filter_enable_25` = '$filter_enable[25]', `filter_enable_26` = '$filter_enable[26]', `filter_enable_27` = '$filter_enable[27]', `filter_enable_28` = '$filter_enable[28]', `filter_enable_29` = '$filter_enable[29]', `filter_enable_30` = '$filter_enable[30]',
		`filter_1` = '$filter[1]', `filter_2` = '$filter[2]', `filter_3` = '$filter[3]', `filter_4` = '$filter[4]', `filter_5` = '$filter[5]', 
		`filter_6` = '$filter[6]', `filter_7` = '$filter[7]', `filter_8` = '$filter[8]', `filter_9` = '$filter[9]', `filter_10` = '$filter[10]', `filter_11` = '$filter[11]', `filter_12` = '$filter[12]', `filter_13` = '$filter[13]', `filter_14` = '$filter[14]', `filter_15` = '$filter[15]', `filter_16` = '$filter[16]', `filter_17` = '$filter[17]', `filter_18` = '$filter[18]', `filter_19` = '$filter[19]', `filter_20` = '$filter[20]', `filter_21` = '$filter[21]', `filter_22` = '$filter[22]', `filter_23` = '$filter[23]', `filter_24` = '$filter[24]', `filter_25` = '$filter[25]', `filter_26` = '$filter[26]', `filter_27` = '$filter[27]', `filter_28` = '$filter[28]', `filter_29` = '$filter[29]', `filter_30` = '$filter[30]',
		`date` = NOW()
		WHERE `id` = '$section_id' LIMIT 1" ;

		$section_sql = mysql_query($section_update_query) or die ("Невозможно обновить данные 3");
		
			
		// --- Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---
		if ($menu_type != $menu_type_last)
		{
			// обновляем не только все пункты, но и подпункты данного меню
			tree($menu_type, $menu_id, 0);
		}
		// --- / Если новый тип меню не равняется старому - запускаем рекурсию смены типа меню у подменюшек ---	
		
		
		
		// --- ЧПУ URL ----------------------------------------------------------------------------------------
		if(isset($sef))
		{
			if(preg_match("/^(admin)|(page)|(shop)|(form)|(article)|(quote)|(users_psw)|(login)|(registration)|(comments)|(notes)|(profile)|(analytics)|(subscribe)|(videochat)|(search)$/is",$sef,$matches)) // зарезервированно
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
					$sef_query = mysql_query("SELECT * FROM `url` WHERE `sef` = '$sef' AND `url` <> 'shop/section/$section_id'") or die ("Ошибка - 1");	
					$sef_query_result = mysql_num_rows($sef_query);
					
					if($sef_query_result == 0) // нет такого `sef` (наш не в счёт)
					{					
						// проверяем - есть ли уже запись
						$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'shop/section/$section_id'") or die ("Ошибка - 1");	
						$url_query_result = mysql_num_rows($url_query);

						if($url_query_result > 0) // запись есть
						{		
							// Обновляем данные в таблице "url"
							$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'shop/section/$section_id'";	
							$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
						}
						else // запись отсутствует
						{
							// Вставляем данные в таблице "url"
							$url_sql = "INSERT INTO `url` (url, sef) VALUES('shop/section/$section_id', '$sef')";		
							$url_query = mysql_query($url_sql) or die ("Ошибка - 2");		
						}
					}
				}	
			}
			
			if($sef == '')
			{
				// Обновляем данные в таблице "url"
				$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'shop/section/$section_id'";	
				$url_query = mysql_query($url_sql) or die ("Ошибка - 4");	
			}				
		}
		// --- / ЧПУ URL / -------------------------------------------------------------------------------------			
		
		
		if($bt_save == 'Сохранить'){Header ("Location: http://".$site."/admin"); exit;}
		else {Header ("Location: http://".$site."/admin/com/shop/sectionedit/".$section_id); exit;}
		
		
} // конец условия заполненного пункта меню



########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $menu_id, $lvl) // $menu_type 1 - верхнее 2 - левое  $page_id = 0 начальный уровень меню, $lvl - уровень меню
{
	global $site;

	$numtree_sql = "SELECT * FROM `menu` WHERE `parent` = '$menu_id' ORDER BY `ordering` ASC";
	
	$numtree = mysql_query($numtree_sql) or die ("Невозможно сделать выборку из таблицы - 3");
	
	$result = mysql_num_rows($numtree);
	
	if ($result > 0) 
	{
		while($m = mysql_fetch_array($numtree)):
			$menu_id = $m['id'];
			$menu_name = $m['name'];
			$menu_id_com = $m['id_com'];
			
			// Обновляем данные в таблице "menu"
			$query_update_menu = "UPDATE `menu` SET `menu_type` = '$menu_type' WHERE `id` = '$menu_id';";	
			$sql_menu = mysql_query($query_update_menu) or die ("Невозможно обновить данные 4");
		
			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu_id, $lvl); 
			
		endwhile;	
		
	} // конец проверки $result > 0
} // конец функции tree
// ==================================================================================
	
function a_com()
{ 
	global $err; 
	echo $err;
} // конец функции

?>
