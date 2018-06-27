<?php
// выводит содержимое товара на отдельной странице.
defined('AUTH') or die('Restricted access');

include_once($root.'/lib/currency.php');

if($frontend_edit == 1){$head->addFile('/components/shop/frontend/item_edit.js');}

// print_r($_SESSION['basket']);

$rnd = mt_rand();
$item_id = intval($d[2]);
$char_md5 = htmlspecialchars($d[3]);

// ======= Проверка существования товара =======================================================
$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `id` = '$item_id' AND `pub` = '1' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 1");

$resulttov = mysql_num_rows($item_query); // количество товаров

while($m = mysql_fetch_array($item_query)):

	$item_id = $m['id'];
	$item_section = $m['section'];
	$item_pub = $m['pub'];
	$item_parent = $m['parent'];
	$item_ordering = $m['ordering'];
	$item_title = $m['title'];
	$item_price = $m['price'];
	$item_price_old = $m['priceold'];
	$item_currency = $m['currency'];
	$item_quantity = $m['quantity'];
	$item_introtext = $m['introtext'];
	$item_fulltext = $m['fulltext'];
	$item_photo = $m['photo'];
	$item_photo_big = $m['photobig'];
	$item_photo_more = $m['photomore'];
	$item_new = $m['new'];
	$item_discount = $m['discount'];
	$tag_title = $m['tag_title'];
	$tag_description = $m['tag_description'];

	$item_title = preg_replace("/\\\/", " ", $item_title);

endwhile;

// Если тег тайтл не заполнен то $page_title + $site_title;
$page_title = $item_title;

switch($item_currency)
{
	case CURRENCY_USD:
	{
		$item_price = CCurrency::usdToRub($item_price);
		$item_price_old = CCurrency::usdToRub($item_price_old);
	} break;

	case CURRENCY_EUR:
	{
		$item_price = CCurrency::eurToRub($item_price);
		$item_price_old = CCurrency::eurToRub($item_price_old);
	} break;
}

$item_price = number_format($item_price, 0, '', ' ');
$item_price_old = number_format($item_price_old, 0, '', ' ');

$item_introtext_d = preg_replace('/(<\/p>)|(<\/div>)|(<br>)|(<br\/>)|(<br \/>)/i', ' ', $item_introtext);

// если тег тайтл не заполнен - заполняем автоматически
if ($tag_description == ""){$tag_description = $item_title.'. Цена: '.$item_price.' '.$shopSettings->getValue('currency').' '.$item_introtext_d;}

// если товаров нет
if ($resulttov == "0")
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}


// ####### Вывод товара ###############################################################
function component()
{
	global $site, $root, $item_id, $item_pub, $item_parent, $item_ordering, $item_title, $item_price, $item_price_old, $item_quantity, $item_introtext, $item_fulltext, $item_photo, $item_photo_big, $item_photo_more, $item_new, $item_discount, $shopSettings, $frontend_edit;

	if($shopSettings->getValue('view_item_card') == 2)
	{
		if(!is_file($root."/components/shop/photo/".$item_photo_big))
		{
			$item_image_size = getimagesize($root.'/components/shop/frontend/tmp/images/no-photo.png');
			$item_image_size_width = $item_image_size[0];
			$item_photobig ='<img class="shop_photo_big" src="/components/shop/frontend/tmp/images/no-photo.png" style="max-width:'.$item_image_size_width.'px;" alt="'.$item_title.'" />';
		}
		else
		{
			$item_image_size = getimagesize($root.'/components/shop/photo/'.$item_photo_big);
			$item_image_size_width = $item_image_size[0];
			$item_photobig ='<img class="shop_photo_big" src="/components/shop/photo/'.$item_photo_big.'" alt="'.$item_title.'" style="max-width:'.$item_image_size_width.'px;" />';
		}

		// если есть дополнительные изображения
		if ($item_photo_more != '')
		{
			$photo_arr = explode(';', $item_photo_more);

			if(count($photo_arr) >= 1)
			{
				for($i=0; $i<count($photo_arr)-1; $i++)
				{
					$photo_arr_big[$i] = str_replace('.jpg', '_.jpg', $photo_arr[$i]);
					$img_more_out .= '<a class="show" style="margin:0px 10px 10px 0px;" href="/components/shop/photo/'.$photo_arr_big[$i].'"><img class="shop_item_photomore" style="width:'.$shopSettings->getValue('x_small').'px; height:'.$shopSettings->getValue('y_small').'px;" src="/components/shop/photo/'.$photo_arr[$i].'" alt=""></a>';
				}
			}
		}
		else{$img_more_out = '';}

		$img_more_out = '<div>'.$img_more_out.'</div>';
	}
	else
	{
		$img_more_out = '';

		// Смотрим существует ли изображение
		if(!is_file($root."/components/shop/photo/".$item_photo_big))
		{
			$item_photobig ='<img class="shop_photo_big" src="/components/shop/frontend/tmp/images/no-photo.png" alt="'.$item_title.'" />';
		}
		else
		{
			// Смотрим на размер картинки
			$size = getimagesize($root.'/components/shop/photo/'.$item_photo_big);

			// Если он больше 600 на 480, то добавляем show2 к фотографии
			if($size[0] > 600 || $size[1] > 480)
			{
				$item_photobig ='<img class="shop_photo_big show2" src="/components/shop/photo/'.$item_photo_big.'" alt="'.$item_title.'" longdesc="/components/shop/photo/'.$item_photo_big.'" />';
			}
			else
			{
				$item_photobig ='<img class="shop_photo_big" src="/components/shop/photo/'.$item_photo_big.'" alt="'.$item_title.'" />';
			}

			// если есть дополнительные изображения
			if ($item_photo_more != '')
			{
				$photo_arr = explode(';', $item_photo_more);

				if(count($photo_arr) >= 1)
				{
					for($i=0; $i<count($photo_arr)-1; $i++)
					{
						$photo_arr_big[$i] = str_replace('.jpg', '_.jpg', $photo_arr[$i]);
						$img_more_out .= '<a class="show" style="margin:0px 10px 10px 0px;" href="/components/shop/photo/'.$photo_arr_big[$i].'"><img class="shop_item_photomore" style="width:'.$shopSettings->getValue('x_small').'px; height:'.$shopSettings->getValue('y_small').'px;" src="/components/shop/photo/'.$photo_arr[$i].'" alt=""></a>';
					}
				}
			}

			$img_more_out = '<div>'.$img_more_out.'</div>';

		} // если есть изображение
	}

	if($item_quantity <= 0 && $shopSettings->getValue('item_quantity') != 0){$item_quantity_status = '<div class="shop_item_qs">'.LANG_UNDER_ORDER_2.'</div>';}else{$item_quantity_status = '';}
	if ($shopSettings->getValue('question')){$item_question_out = '<a href="#" onclick="DAN_modal(\'450\', \'500\', \'\', item_question)" class="shop_item_question">'.LANG_ASK_ABOUT_PRODUCT.'</a>';} else{{$item_question_out = '';}}

	// ====== Характеристики ==========================================================

	$chars_query = mysql_query(
	"SELECT `c`.`id`, `c`.`item_id`, `c`.`name_id`, `n`.`unit`, `n`.`name`, `n`.`type`,
	CASE
	WHEN `n`.`type` = 'number' THEN `vi`.`value`
	WHEN `n`.`type` = 'string' THEN `vs`.`value`
	END as value, `c`.`value_id`
	FROM `com_shop_char` `c`
	JOIN `com_shop_char_name` `n` ON `n`.`id` = `c`.`name_id`
	LEFT OUTER JOIN `com_shop_char_value_number` `vi` ON `vi`.`id` = `c`.`value_id`
	LEFT OUTER JOIN `com_shop_char_value_string` `vs` ON `vs`.`id` = `c`.`value_id`
	WHERE `c`.`item_id` = '$item_id' ORDER BY `c`.`ordering`"
	) or die ("Невозможно сделать выборку из таблицы - 1");

	$item_arr = array();

	while($m = mysql_fetch_array($chars_query))
	{
		$id = $m['id'];
		$item_id = $m['item_id'];
		$name_id = $m['name_id'];
		$name = $m['name'];
		$unit = $m['unit'];
		$type = $m['type'];
		$value_id = $m['value_id'];
		$value = $m['value'];

		// Заносим всё в массив
		$item_arr[$name]['item_id'] = $item_id;
		$item_arr[$name]['unit'] = $unit;
		$item_arr[$name]['type'] = $type;
		$item_arr[$name]['value'][] = $value;
	}


	$item_char = '';
	$item_char_tr = '';



	// перебираем массив по названиям характеристик
	foreach ($item_arr as $name => $char)
	{
		if(count($char['value']) > 1)
		{
			$value_out = '<select name="char['.$name.']">';
			// перебираем характеристику
			foreach ($char['value'] as $otion_value)
			{
				$value_out .= "<option>".$otion_value."</option>\n";
			}
			$value_out .= '</select>';
		}
		else
		{
			$value_out = $char['value'][0];
		}

		if($char['unit'] == ''){$unit_out = '';} else {$unit_out = ', '.$char['unit'];}

		$item_char_tr .= '<tr>';
		$item_char_tr .= '<td class="shop_char_td_1">'.$name.$unit_out.'</td>';
		$item_char_tr .= '<td class="shop_char_td_2">'.$value_out.'</td>';
		$item_char_tr .= '</tr>';
	}

	$item_char .= '<table class="shop_char_table">';
	$item_char .= $item_char_tr;
	$item_char .= '</table>';



	// Подключаем шаблон товара
	include("components/shop/frontend/tmp/shop_item_tmp.php");

} // конец функции component


// ================================================================================================

// подключение модального окна "Задать вопрос по этому товару"
$head->addCode('
	<script type="text/javascript">
	var item_question = \'<form method="post" action="/shop/question">\';
	item_question += \'<div class="shop_item_question_form">\';
	item_question += \'<div class="shop_item_question_title">Задать вопрос по этому товару:</div>\';
	item_question += \'<div class="shop_item_question_name">\';
	item_question += \''.$item_title.'<input type="hidden" name="item_title" value="'.$item_title.'"><input type="hidden" name="item_id" value="'.$item_id.'">\';
	item_question += \'<input type="hidden" name="item_id" value="'.$item_id.'">\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<input type="email" name="email" size="20" placeholder="Ваш Email" class="shop_item_question_element" autocomplete="off" maxlength="30" required title="Неверно указан email">\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<textarea name="question" rows="3" cols="45" placeholder="Ваш Вопрос" class="shop_item_question_textarea" ></textarea>\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<img class="shop_item_captcha" src="/administrator/captcha/pic.php?'.$rnd.'">\';
	item_question += \'<input type="text" name="captcha" size="4" class="shop_item_question_element shop_item_captcha_input" autocomplete="off" maxlength="4" required pattern="[0-9]{4}"  title="Введите 4 цифры с картинки">\';
	item_question += \'<span class="shop_item_captcha_text">Введите цифры с картинки</span>\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<input type="submit" value="Отправить" name="send" class="shop_item_button">\';
	item_question += \'</div>\';
	item_question += \'</div>\';
	item_question += \'</form>\';
	</script>
');



?>
