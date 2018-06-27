<?php
defined('AUTH') or die('Restricted access');

$item_id = intval($d[5]);

include_once($root.'/components/shop/classes/Chars.php');
$chars = new Chars($item_id);

$identifier = trim(htmlspecialchars_decode($_POST["identifier"]));
$group_identifier = trim(htmlspecialchars_decode($_POST["group_identifier"]));
$section = intval($_POST["section"]);
$pub = intval($_POST["pub"]);
if(isset($_POST["parent"])){$parent = intval($_POST["parent"]);} else {$parent = 0;}
$ordering = intval($_POST["ordering"]);
$title = trim($_POST["title"]);
$price = intval($_POST["price"]);
if(isset($_POST["discount"])){$discount = intval($_POST["discount"]);} else {$discount = 0;}
$price_old = intval($_POST["price_old"]);
$currency = intval($_POST["currency"]);
if(isset($_POST["new"])){$new = intval($_POST["new"]);} else {$new = 0;}
if(isset($_POST["hit"])){$hit = intval($_POST["hit"]);} else {$hit = 0;}
$rating = $_POST["rating"];
if(isset($_POST["quantity"])){$quantity = floatval($_POST["quantity"]);}else{$quantity = 1;}
$images_order = htmlspecialchars($_POST["images_order"]);
$related_items = $_POST['related_order'];
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
if(isset($_POST["sef"])){$sef = checkingeditor($_POST["sef"]);}
$intro_text = $_POST["editor1"];
$full_text = $_POST["editor2"];
if(isset($_POST["etext_enabled"])){$etext_enabled = intval($_POST["etext_enabled"]);} else {$etext_enabled = 0;}
$etext = $_POST["editor3"];

if(isset($_POST["char_id"])){$char_id_arr = $_POST["char_id"];} else{$char_id_arr = '';}
if(isset($_POST["char_name_id"])){$char_name_id_arr = $_POST["char_name_id"];} else{$char_name_id_arr = '';}
if(isset($_POST["char_value"])){$char_value_arr = $_POST["char_value"];} else{$char_value_arr = '';}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'


// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/section/".$section); exit;}

$title = replace_quotes($title);

if (!isset($pub) || $pub == ""){$pub = "0";} else{$pub = "1";} // Условие публикации
if (!isset($parent) || $parent == ""){$parent = "0";} // условие публикации

$rating = floatval(str_replace(',', '.', $rating));
$rating = $rating < 0 ? 0 : $rating;
$rating = $rating > 5 ? 5 : $rating;

if (isset($artikul) && $artikul <> '')
{
	// ======= ИЩЕМ ТОВАР С ТАКИМ ЖЕ АРТИКУЛОМ =======

	$stmt_artikul = $db->prepare("SELECT * FROM com_shop_item WHERE identifier = :identifier AND id <> :id  LIMIT 1");
	$stmt_artikul->execute(array('identifier' => $artikul, 'id' => $item_id));

	if ($stmt_artikul->rowCount() > 0)
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
	$stmt_artikul = $db->prepare('
	UPDATE com_shop_item SET
	identifier = :identifier,
	group_identifier = :group_identifier,
	section = :section,
	pub = :pub,
	parent = :parent,
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
	photo_more = :photo_more,
	new = :new,
	discount = :discount,
	hit = :hit,
	rating = :rating,	
	cdate = :cdate,
	tag_title = :tag_title,
	tag_description = :tag_description
	WHERE id = :id
	');


	$stmt_artikul->execute(array(
	'id' => $item_id,
	'identifier' => $identifier,
	'group_identifier' => $group_identifier,
	'section' => $section,
	'pub' => $pub,
	'parent' => $parent,
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
	'photo' => $photo_small,
	'photo_big' => $photo_big,
	'photo_more' => $photo_more,
	'new' => $new,
	'discount' => $discount,
	'hit' => $hit,
	'rating' => $rating,
	'cdate' => date("Y-m-d H:i:s"),
	'tag_title' => $tag_title,
	'tag_description' => $tag_description
	));


	// --- ТИПЫ ЦЕН ---
	if(!empty($_POST['price_user']))
	{
		foreach($_POST['price_user'] as $price_type_id => $price_item)
		{
			$stmt_pi = $db->prepare("SELECT id FROM com_shop_price_item WHERE item_id = :item_id AND price_type_id = :price_type_id LIMIT 1");
			$stmt_pi->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id));
			$pi_id = $stmt_pi->fetchColumn();

			if(!empty($price_item))
			{
				if($stmt_pi->rowCount() > 0) // Уже есть эта цена - обновляем
				{
					$stmt_pi_update = $db->prepare("UPDATE com_shop_price_item SET price = :price WHERE id = :id");
					$stmt_pi_update->execute(array('id' => $pi_id, 'price' => $price_item));			
				}
				else // Нет цены - добавляем
				{
					$stmt_pi_update = $db->prepare("INSERT INTO com_shop_price_item SET item_id = :item_id, price_type_id = :price_type_id, price = :price");
					$stmt_pi_update->execute(array('item_id' => $item_id, 'price_type_id' => $price_type_id, 'price' => $price_item));
				}
			}
			else // Удаляем пустые значения, незачем хранить мусор
			{
				if($stmt_pi->rowCount() > 0) // Уже есть эта цена - удаляем
				{
					$stmt_pi_delete = $db->prepare("DELETE FROM com_shop_price_item WHERE id = :id");
					$stmt_pi_delete->execute(array('id' => $pi_id));		
				}
			}
		}
	}


	// Добавляем / обновляем характеристики
	if ($char_id_arr != '' && $char_value_arr != '')
	{
		$count = count($char_id_arr);

		for ($i = 0; $i < $count; $i++)
		{
			// Определяем есть ли эта характиристика
			$stmt_char = $db->prepare('SELECT id FROM com_shop_char WHERE id = :id LIMIT 1');
			$stmt_char->execute(array('id' => $char_id_arr[$i]));

			if($stmt_char->rowCount() > 0) // характеристика уже есть
			{
				Chars::updateChar($char_id_arr[$i], $item_id, $char_name_id_arr[$i], $char_value_arr[$i], $i, '1');
			}
			else // характеристики нет
			{
				Chars::addChar($item_id, $char_name_id_arr[$i], $char_value_arr[$i], $i, '1');
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
				// Ищем, есть ли данный товар уже в БД и если есть - проверяем изменилось ли значение ordering - если да, обновляем товар
				$stmt_related = $db->prepare("SELECT ordering FROM com_shop_related_item WHERE id = :id LIMIT 1");
				$stmt_related->execute(array('id' => $value));

				// Если порядок следования в БД !=  порядку в input ($key)
				if($stmt_related->fetchColumn() != $key)
				{
					$stmt_related_update = $db->prepare("UPDATE com_shop_related_item SET ordering = :ordering WHERE id = :id LIMIT 1");
					$stmt_related_update->execute(array('ordering' => $key, 'id' => $value));
				}
			}
		}
	}
	
	
	
	if($bt_save == 'Сохранить'){Header ("Location: /admin/com/shop/section/".$section); exit;}
	else {Header ("Location: /admin/com/shop/item/edit/".$item_id); exit;}

} // конец условия заполненного пункта меню


function a_com()
{
	global $err;
	echo $err;
} // конец функции
?>
