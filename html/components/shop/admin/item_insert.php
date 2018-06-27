<?php
// DAN обновление - январь 2014
// Вставляем данные в базу данных

defined('AUTH') or die('Restricted access');

include ($root.'/lib/image_processor.php');

$title = htmlspecialchars($_POST["title"]);
$artikul = htmlspecialchars($_POST["artikul"]);
$section = intval($_POST["section"]);
$price = intval($_POST["price"]);
$discount = intval($_POST["discount"]);
$price_old = intval($_POST["price_old"]);
$new = intval($_POST["new"]);
if ($_POST["quantity"] == ''){$quantity = 1;}
else {$quantity = floatval($_POST["quantity"]);}
$images_order = htmlspecialchars($_POST["images_order"]);
$pub = intval($_POST["pub"]);
$ordering = intval($_POST["ordering"]);
$menu_t = htmlspecialchars($_POST["menu_t"]);
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

// увеличиваем время выполнения
set_time_limit(60);
// выставить лимит в 512Mb
ini_set('memory_limit', '512M');
// лимит памяти
$memory_limit = get_cfg_var('memory_limit');

$memory_limit = (real)$memory_limit;

$ph_name = checkingeditor_2($_FILES['photo']['name']); // Оригинальное имя файла на компьютере клиента.
$file_size = checkingeditor_2($_FILES['photo']['size']); // Размер в байтах принятого файла.

$ph = $_FILES['photo']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.

if (isset($artikul) && $artikul <> '')
{
	// ======= ИЩЕМ ТОВАР С ТАКИМ ЖЕ АРТИКУЛОМ =======
	$item_artikul_sql= "SELECT * FROM `com_shop_item` WHERE `artikul` = '$artikul' LIMIT 1";

	$item_artikul_query = mysql_query($item_artikul_sql) or die ("Невозможно сделать выборку из таблицы - 1");
	$item_artikul_result = mysql_num_rows($item_artikul_query);


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


if ($file_size >= 5000000) // Проверка размера файла
{
	die ('
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Файл слишком большой!</title>
	</head>
	<body>
	<h3 align="center">Файл слишком большой! Максимальный размер файла не более 5 мб</h3>
	</body>
	</html>
	');
}

if (!isset($pub) || $pub == ""){$pub = "0";} // Условие публикации

$err = '';

// условия заполнения полей
if ($section == 0){$err .= '<div id="main-top">Не указан раздел!</div>';}


if ($title == '' || $title == ' ')
{
	$err .= '<div id="main-top">Поле "Наименование товара" не заплонено!</div>';
}

if ($price === '' || $price === ' ')
{
	$err .= '<div id="main-top">Поле "Цена товара" не заплонена!</div>';
}

if($err == '')
{
	// Находим максимальное значение поля ordering

	if ($ordering == 0)
	{
		$mo = "SELECT max(ordering) `ordering` FROM `com_shop_item`";
		$mxo = mysql_query($mo);
		while($maxord = mysql_fetch_array($mxo)):
			$maxordering = $maxord['ordering'];
		endwhile;

		$maxordering++;
		
		$ordering = $maxordering;
	}
	
	
	// === Подключение настроек ============================================================	
	$shop_setting_sql = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

	while($m = mysql_fetch_array($shop_setting_sql))
	{
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parametr = $m['parametr'];

		// размер по "х" малого изображения
		if ($setting_name == "x_small")
		{
			$w_small = $setting_parametr;
		}

		// размер по "y" малого изображения
		if ($setting_name == "y_small")
		{
			$h_small = $setting_parametr;
		}

		// размер по "х" большого изображения
		if ($setting_name == "x_big")
		{
			$w_big = $setting_parametr;
		}

		// размер по "y" большого изображения
		if ($setting_name == "y_big")
		{
			$h_big = $setting_parametr;
		}

		// метод ресайза
		if ($setting_name == "small_resize_method")
		{
			$act = $setting_parametr;
		}
	}

	
	$img_dir = $root.'/components/shop/photo/';
	$img_name = date('ymdHis').'.jpg';


	// === Загрузка фото ==============================================================================
	$photo_dir='components/shop/photo/';
	$tmp_file = $photo_dir."temp_file.jpg";

	if(file_exists($ph))
	{
		Copy($ph,"$photo_dir".basename($tmp_file));		
		$img_arr = img_load($act, $img_name, $img_dir, $tmp_file, $w_small, $h_small, $w_big, $h_big, '', '', '', '', '', '', '');
		
		$img_small = $img_arr[0];
		$img_big = $img_arr[1];
	}
	else {$img_small = ''; $img_big = '';}

	// Вставляем в таблицу "com_shop_item"
	$query_insert_page = "INSERT INTO `com_shop_item` (`id`, `identifier`,`section`, `pub`, `parent`, `ordering`, `title`, `introtext`, `fulltext`, `etext_enabled`, `etext`, `price`, `priceold`, `quantity`, `photo`, `photobig`, `photomore`, `new`, `discount`, `cdate`, `tag_title`, `tag_description`, `characteristic_1`, `characteristic_2`, `characteristic_3`, `characteristic_4`, `characteristic_5`, `characteristic_6`, `characteristic_7`, `characteristic_8`, `characteristic_9`, `characteristic_10`, `characteristic_11`, `characteristic_12`, `characteristic_13`, `characteristic_14`, `characteristic_15`, `characteristic_16`, `characteristic_17`, `characteristic_18`, `characteristic_19`, `characteristic_20`, `characteristic_21`, `characteristic_22`, `characteristic_23`, `characteristic_24`, `characteristic_25`, `characteristic_26`, `characteristic_27`, `characteristic_28`, `characteristic_29`, `characteristic_30`) 
	VALUES (NULL, '$artikul', '$section', '$pub', '0', '$ordering', '$title', '$introtext', '$fulltext', '$etext_enabled', '$etext', '$price', '$price_old', '$quantity', '$img_small', '$img_big', '', '$new', '$discount', NOW(), '$tag_title', '$tag_description', '$characteristic[1]', '$characteristic[2]', '$characteristic[3]', '$characteristic[4]', '$characteristic[5]', '$characteristic[6]', '$characteristic[7]', '$characteristic[8]', '$characteristic[9]', '$characteristic[10]', '$characteristic[11]', '$characteristic[12]', '$characteristic[13]', '$characteristic[14]', '$characteristic[15]', '$characteristic[16]', '$characteristic[17]', '$characteristic[18]', '$characteristic[19]', '$characteristic[20]', '$characteristic[21]', '$characteristic[22]', '$characteristic[23]', '$characteristic[24]', '$characteristic[25]', '$characteristic[26]', '$characteristic[27]', '$characteristic[28]', '$characteristic[29]', '$characteristic[30]')";

	$sql_page = mysql_query($query_insert_page) or die ("Невозможно обновить данные 1");
	$item_id = mysql_insert_id();


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

}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции


function shop_item_char_zapros($str) {
//$pattern = "/(\')|(\")|(\+)|[^\x20-\xFF]/";
// Запретим использовать любые символы, кроме букв русского и латинского алфавита, знака "_", "-", "/", пробела, точки, запятой, точки с запятой и цифр
$pattern = "/[^(\w)|(\/)|(\-)||(\_)|(\.)|(\,)|(\;)|(\s)(\x7F-\xFF)]/";
$replacement = "";
return preg_replace($pattern, $replacement, $str);
}

?>
