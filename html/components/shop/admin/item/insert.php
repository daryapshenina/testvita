<?php
// DAN обновление - январь 2014
// Вставляем данные в базу данных
defined('AUTH') or die('Restricted access');

include ($root.'/lib/image_processor.php');
include_once($root.'/components/shop/classes/Chars.php');
$chars = new Chars;

$title = trim(htmlspecialchars($_POST["title"]));
$identifier = trim(htmlspecialchars_decode($_POST["identifier"]));
$group_identifier = trim(htmlspecialchars_decode($_POST["group_identifier"]));
$section = intval($_POST["section"]);
$price = intval($_POST["price"]);
if(isset($_POST["discount"])){$discount = intval($_POST["discount"]);} else{$discount = 0;}
$price_old = intval($_POST["price_old"]);
$currency = intval($_POST["currency"]);
if(isset($_POST["new"])){$new = intval($_POST["new"]);} else{$new = 0;}
if(isset($_POST["hit"])){$hit = intval($_POST["hit"]);} else {$hit = 0;}
$rating = $_POST["rating"];
$quantity = floatval($_POST["quantity"]);
$pub = intval($_POST["pub"]);
$ordering = intval($_POST["ordering"]);
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
$related_items = $_POST['related_order'];
$intro_text = $_POST["editor1"];
$full_text = $_POST["editor2"];
if(isset($_POST["etext_enabled"])){$etext_enabled = intval($_POST["etext_enabled"]);} else{$etext_enabled = 0;}
$etext = $_POST["editor3"];

if(isset($_POST["char_id"])){$char_id_arr = $_POST["char_id"];} else{$char_id_arr = '';}
if(isset($_POST["char_name_id"])){$char_name_id_arr = $_POST["char_name_id"];} else{$char_name_id_arr = '';}
if(isset($_POST["char_value"])){$char_value_arr = $_POST["char_value"];} else{$char_value_arr = '';}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/section/".$section."/".$menu_t); exit;}

// увеличиваем время выполнения
set_time_limit(60);
// выставить лимит в 512Mb
ini_set('memory_limit', '512M');
// лимит памяти
$memory_limit = get_cfg_var('memory_limit');

$memory_limit = (real)$memory_limit;

$title = replace_quotes($title);

$rating = floatval(str_replace(',', '.', $rating));
$rating = $rating < 0 ? 0 : $rating;
$rating = $rating > 5 ? 5 : $rating;

$ph_name = checkingeditor_2($_FILES['photo']['name']); // Оригинальное имя файла на компьютере клиента.
$file_size = checkingeditor_2($_FILES['photo']['size']); // Размер в байтах принятого файла.

$ph = $_FILES['photo']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.



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
		$stmt_item = $db->query('SELECT max(ordering) ordering FROM com_shop_item');
		$row_item = $stmt_item->fetch();

		$ordering = $row_item['ordering'] + 1;
	}

	$img_dir = $root.'/components/shop/photo/';
	$img_name = date('ymdHis').'.jpg';


	// === Загрузка фото ==============================================================================
	$photo_dir='components/shop/photo/';
	$tmp_file = $photo_dir."temp_file.jpg";

	if(file_exists($ph))
	{
		Copy($ph,"$photo_dir".basename($tmp_file));
		$img_arr = img_load($shopSettings->small_resize_method, $img_name, $img_dir, $tmp_file, $shopSettings->x_small, $shopSettings->y_small, $shopSettings->x_big, $shopSettings->y_big);

		$img_small = $img_arr[0];
		$img_big = $img_arr[1];
	}
	else {$img_small = ''; $img_big = '';}

	$stmt_item_insert = $db->prepare('
	INSERT INTO com_shop_item SET
	identifier = :identifier,
	group_identifier = :group_identifier,
	section = :section,
	pub = :pub,
	parent = \'0\',
	ordering = :ordering,
	title = :title,
	intro_text = :intro_text,
	full_text = :full_text,
	etext_enabled = :etext_enabled,
	etext = :etext,
	price = :price,
	price_old = :price_old,
	currency = :currency,
	quantity = :quantity,
	photo = :photo,
	photo_big = :photo_big,
	photo_more = \'\',
	new = :new,
	hit = :hit,
	rating = :rating,
	discount = :discount,
	cdate = :cdate,
	tag_title = :tag_title,
	tag_description = :tag_description
	');

	$stmt_item_insert->execute(array(
	'section' => $section,
	'identifier' => $identifier,
	'group_identifier' => $group_identifier,
	'pub' => $pub,
	'ordering' => $ordering,
	'title' => $title,
	'intro_text' => $intro_text,
	'full_text' => $full_text,
	'etext_enabled' => $etext_enabled,
	'etext' => $etext,
	'price' => $price,
	'price_old' => $price_old,
	'currency' => $currency,
	'quantity' => $quantity,
	'photo' => $img_small,
	'photo_big' => $img_big,
	'new' => $new,
	'hit' => $hit,
	'rating' => $rating,	
	'discount' => $discount,
	'cdate' => date("Y-m-d H:i:s"),
	'tag_title' => $tag_title,
	'tag_description' => $tag_description
	));

	$item_id = $db->lastInsertId();


	// --- ТИПЫ ЦЕН ---
	if(!empty($_POST['price_user']))
	{
		foreach($_POST['price_user'] as $price_type_id => $price_item)
		{
			$stmt_pi = $db->prepare("SELECT id FROM com_shop_price_item WHERE item_id = :item_id AND price_type_id = :price_type_id LIMIT 1");
			$stmt_pi->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id));
			$pi_id = $stmt_pi->fetchColumn();

			if(!empty($price_item) && $price_item != 0)
			{
				$stmt_pi_update = $db->prepare("INSERT INTO com_shop_price_item SET item_id = :item_id, price_type_id = :price_type_id, price = :price");
				$stmt_pi_update->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id, 'price' => $price_item));
			}
		}
	}	


	// Добавляем характеристики
	if ($char_id_arr != '' && $char_value_arr != '')
	{
		$count = count($char_id_arr);
		for ($i = 0; $i < $count; $i++)
		{
			Chars::addChar($item_id, $char_name_id_arr[$i], $char_value_arr[$i], $i, '1');
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

				$stmt_url = $db->prepare('SELECT id FROM url WHERE url =:url LIMIT 1');
				$stmt_url->execute(array('url' => 'shop/item/'.$item_id));

				if($stmt_url->rowCount() > 0) // запись есть
				{
					$stmt_url_update = $db->prepare('UPDATE url SET sef = :sef WHERE url = :url');
					$stmt_url_update->execute(array('sef' => $sef, 'url' => 'shop/item/'.$item_id));
				}
				else // запись отсутствует
				{
					$stmt_url_insert = $db->prepare('INSERT INTO url SET url = :url, sef = :sef');
					$stmt_url_insert->execute(array('url' => 'shop/item/'.$item_id, 'sef' => $sef));
				}
			}
		}

		if($sef == '')
		{
			// Обновляем данные в таблице "url"
					$stmt_url_update = $db->prepare('UPDATE url SET sef = \'\' WHERE url = :url');
					$stmt_url_update->execute(array('url' => 'shop/item/'.$item_id));
		}
	}
	// --- / ЧПУ URL / -------------------------------------------------------------------------------------


	// --- СОПУТСТВУЮЩИЕ ТОВАРЫ ----------------------------------------------------------------------------
	$related_items_arr = explode(';', $related_items);
	if(count($related_items_arr) > 0)
	{
		foreach($related_items_arr as $key => $value)
		{
			if(!empty($value))
			{
				$stmt_related_insert = $db->prepare('INSERT INTO com_shop_related_item SET item_id = :item_id, related_id = :related_id, ordering = :ordering');
				$stmt_related_insert->execute(array('item_id' => $item_id, 'related_id' => $value, 'ordering' => $key));
			}
		}
	}

	if($bt_save == 'Сохранить'){Header ("Location: /admin/com/shop/section/".$section."/".$menu_t); exit;}
	else {Header ("Location: /admin/com/shop/item/edit/".$item_id); exit;}

}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции
?>
