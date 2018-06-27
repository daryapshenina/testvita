<?php
// DAN обновление - январь 2014
// Вставляем данные в базу данных
defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4);
$artikul = htmlspecialchars($_POST["artikul"]);
$section = intval($_POST["section"]);
$pub = intval($_POST["pub"]);
$parent = intval($_POST["parent"]);
$ordering = intval($_POST["ordering"]);
$title = htmlspecialchars($_POST["title"]);
$price = intval($_POST["price"]);
$discount = intval($_POST["discount"]);
$price_old = intval($_POST["price_old"]);
$new = intval($_POST["new"]);
if ($_POST["quantity"] == ''){$quantity = 1;}
else {$quantity = floatval($_POST["quantity"]);}
$images_order = htmlspecialchars($_POST["images_order"]);
$tag_title = htmlspecialchars($_POST["tag_title"]);
$tag_description = htmlspecialchars($_POST["tag_description"]);
$sef = checkingeditor($_POST["sef"]);
$introtext = $_POST["editor1"];
$fulltext = $_POST["editor2"];
$etext_enabled = intval($_POST["etext_enabled"]);
$etext = $_POST["editor3"];

for($i=1; $i<=25; $i++)
{
	$characteristic[$i] = shop_item_char_zapros($_POST["characteristic_".$i]);
}

for($i=26; $i<=30; $i++)
{
	$characteristic[$i] = floatval($_POST["characteristic_".$i]);
}

$bt_save = $_POST["bt_save"]; // кнопка 'Сохранить'
$bt_prim = $_POST["bt_prim"]; // кнопка 'Применить'
$bt_none = $_POST["bt_none"]; // кнопка 'Отменить'



// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: http://".$site."/admin/com/shop/section/".$section."/".$menu_t); exit;}

// определяем тип меню
$menu_t = intval($admin_d5);


if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";} // Условие публикации
if (!isset($parent) || $parent == ""){$parent = "0";} // условие публикации


if (isset($artikul) && $artikul <> '')
{
	// ======= ИЩЕМ ТОВАР С ТАКИМ ЖЕ АРТИКУЛОМ =======
	$item_artikul_query = "SELECT * FROM `com_shop_item` WHERE `identifier` = '$artikul' AND `id` <> '$item_id'  LIMIT 1";

	$item_artikul = mysql_query($item_artikul_query) or die ("Невозможно сделать выборку из таблицы - 1");

	$item_artikul_result = mysql_num_rows($item_artikul);

	if ($item_artikul_result > 0)
	{
		die ('
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Товар с таким арикулом уже существует</title>
		</head>
		<body>
		<h3 align="center">Товар с таким арикулом - <font color="#FF0000">'.$artikul.'</font> - уже существует</h3>
		</body>
		</html>
		');
	}
	// ======= / ищем товар с таким же артикулом =======
}

if ($title == "" || $title == " ")
{
	$err = '<div id="main-top">Поле "Наименование товара" не заплонено!</div>';
}
else {

	$photomore_arr = explode(';', $images_order);
	$photo_small = $photomore_arr[0]; // главная фотография
	$photo_big = str_replace('.jpg', '_.jpg', $photo_small);
	$photo_more = str_replace($photomore_arr[0].';', '', $images_order); // удаляем следующую за главной фотографию, т.к. она становиться главной

	
	// Обновляем данные в таблице "com_shop_item" если не стоит галочка 'редактировать фото'
	$query_update_shop_item = "UPDATE `com_shop_item` SET `identifier` = '$artikul', `section` = '$section', pub = '$pub', `parent` = $parent, `ordering` = '$ordering', `title` = '$title', `introtext` = '$introtext', `fulltext` = '$fulltext', `etext_enabled` = '$etext_enabled', `etext` = '$etext', `price` = '$price', `priceold` = '$price_old ', `quantity` = '$quantity', `photo` = '$photo_small', `photobig` = '$photo_big', `photomore` = '$photo_more', `new` = '$new', `discount` = '$discount', `cdate` = NOW(), `tag_title` = '$tag_title', `tag_description` = '$tag_description', `characteristic_1` = '$characteristic[1]', `characteristic_2` = '$characteristic[2]', `characteristic_3` = '$characteristic[3]', `characteristic_4` = '$characteristic[4]', `characteristic_5` = '$characteristic[5]', `characteristic_6` = '$characteristic[6]', `characteristic_7` = '$characteristic[7]', `characteristic_8` = '$characteristic[8]', `characteristic_9` = '$characteristic[9]', `characteristic_10` = '$characteristic[10]', `characteristic_11` = '$characteristic[11]', `characteristic_12` = '$characteristic[12]', `characteristic_13` = '$characteristic[13]', `characteristic_14` = '$characteristic[14]', `characteristic_15` = '$characteristic[15]', `characteristic_16` = '$characteristic[16]', `characteristic_17` = '$characteristic[17]', `characteristic_18` = '$characteristic[18]', `characteristic_19` = '$characteristic[19]', `characteristic_20` = '$characteristic[20]', `characteristic_21` = '$characteristic[21]', `characteristic_22` = '$characteristic[22]', `characteristic_23` = '$characteristic[23]', `characteristic_24` = '$characteristic[24]', `characteristic_25` = '$characteristic[25]', `characteristic_26` = '$characteristic[26]', `characteristic_27` = '$characteristic[27]', `characteristic_28` = '$characteristic[28]', `characteristic_29` = '$characteristic[29]', `characteristic_30` = '$characteristic[30]' WHERE `id` = '$item_id' LIMIT 1 ;";

	$sql_shop_item = mysql_query($query_update_shop_item) or die ("Невозможно обновить данные 1");


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

				// проверяем - есть ли уже запись
				$url_query = mysql_query("SELECT * FROM `url` WHERE `url` = 'shop/item/$item_id'") or die ("Ошибка - 1");
				$url_query_result = mysql_num_rows($url_query);

				if($url_query_result > 0) // запись есть
				{
					// Обновляем данные в таблице "url"
					$url_sql = "UPDATE `url` SET `sef` = '$sef' WHERE `url` = 'shop/item/$item_id'";
					$url_query = mysql_query($url_sql) or die ("Ошибка - 2");
				}
				else // запись отсутствует
				{
					// Вставляем в таблицу
					$url_sql = "INSERT INTO `url` (id, url, sef) VALUES(NULL, 'shop/item/$item_id', '$sef')";
					$url_query = mysql_query($url_sql) or die ("Ошибка - 3") ;
				}
			}
		}

		if($sef == '')
		{
			// Обновляем данные в таблице "url"
			$url_sql = "UPDATE `url` SET `sef` = '' WHERE `url` = 'shop/item/$item_id'";
			$url_query = mysql_query($url_sql) or die ("Ошибка - 4");
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------


	if($bt_save == 'Сохранить'){Header ("Location: http://".$site."/admin/com/shop/section/".$section."/".$menu_t); exit;}
	else {Header ("Location: http://".$site."/admin/com/shop/itemedit/".$item_id); exit;}

} // конец условия заполненного пункта меню

// ==================================================================================

function a_com()
{
	global $err;
} // конец функции


function shop_item_char_zapros($str) {
//$pattern = "/(\')|(\")|(\+)|[^\x20-\xFF]/";
// Запретим использовать любые символы, кроме букв русского и латинского алфавита, знака "_", "-", "/", пробела, точки, запятой, точки с запятой и цифр
$pattern = "/[^(\w)|(\/)|(\-)||(\_)|(\.)|(\,)|(\;)|(\s)(\x7F-\xFF)]/";
$replacement = "";
return preg_replace($pattern, $replacement, $str);
}

?>
