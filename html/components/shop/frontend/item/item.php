<?php
defined('AUTH') or die('Restricted access');

// include_once($root.'/lib/currency.php');
include_once $root."/classes/MobileDetector.php";
include_once __DIR__.'/lang/'.LANG.'.php';

// Авторизация пользователя
include_once($root."/classes/Auth.php");
$u = Auth::check();

$ue = (float)$shopSettings->ue; // Условные единицы

// Цены пользователя
if(!empty($u))
{
	$stmt_pu = $db->prepare("SELECT u.price_type_id, t.name FROM com_shop_price_user u JOIN com_shop_price_type t ON t.id = u.price_type_id  WHERE user_id = :user_id LIMIT 1");
	$stmt_pu->execute(array('user_id' => $u));
	$p = $stmt_pu->fetch();
	$price_type = '<div class="price_type">Тип цены: '.$p['name'].'</div>';

	if($stmt_pu->rowCount() > 0)
	{
		$SQL_pu = 'pi.price price_u,';
		$SQL_pu_case = ",
			CASE currency
			WHEN '0' THEN pi.price
			WHEN '1' THEN pi.price * ".CCurrency::getUSD()."
			WHEN '2' THEN pi.price * ".CCurrency::getEUR()."
			WHEN '3' THEN pi.price * ".$ue."
			END as price_user
		";
		$SQL_pu_join = "LEFT JOIN com_shop_price_item pi ON pi.item_id = i.id AND pi.price_type_id = '".$p['price_type_id']."' ";
	}
	else
	{
		$price_type = '';
		$price_type_id = false;
		$SQL_pu = '';
		$SQL_pu_case = '';
		$SQL_pu_join = '';
	}
}
else
{
	$price_type = '';
	$price_type_id = false;
	$SQL_pu = '';
	$SQL_pu_case = '';
	$SQL_pu_join = '';
}

CCurrency::update();

$rnd = mt_rand();
$id = intval($d[2]);

$items_arr = array();

// Если включена группировка
if($shopSettings->grouping == 1 || $shopSettings->view_item_card == 4 || $shopSettings->view_item_card == 5)
{
	$sql_group = "
	OR i.group_identifier IN (
		SELECT group_identifier
		FROM com_shop_item
		WHERE id = :id AND group_identifier != ''
	)
	";
}
else {$sql_group = "";}

// Находим все товары с group_identifier = group_identifier текущего товара
$SQL = "SELECT
i.id,
i.group_identifier,
i.title,
i.intro_text,
i.full_text,
i.etext_enabled,
i.etext,
i.currency,
i.quantity,
i.photo,
i.photo_big,
i.photo_more,
i.new,
i.hit,
i.rating,
i.discount,
i.tag_title,
i.tag_description,
".$SQL_pu."
CASE i.currency
WHEN '0' THEN i.price
WHEN '1' THEN i.price * ".CCurrency::getUSD()."
WHEN '2' THEN i.price * ".CCurrency::getEUR()."
WHEN '3' THEN i.price * ".$ue."
END as price,
CASE i.currency
WHEN '0' THEN i.price_old
WHEN '1' THEN i.price_old * ".CCurrency::getUSD()."
WHEN '2' THEN i.price_old * ".CCurrency::getEUR()."
WHEN '3' THEN i.price * ".$ue."
END as price_old
".$SQL_pu_case."
FROM com_shop_item i
".$SQL_pu_join."
WHERE i.id = :id
".$sql_group."
AND pub = '1'
ORDER BY ordering";

$stmt_items = $db->prepare($SQL);

$stmt_items->execute(array('id' => $id));

$n = 0;
while($i = $stmt_items->fetch())
{
	if($i['price'] > 999999999)$i['price'] = 0;
	if(!empty($i['price_user'])){$i['price'] = $i['price_user'];} // Цена установленная для зарегистрированного пользователя

	$id_i = $i['id'];
	$items_arr[$id_i]['id'] = $i['id'];
	$items_arr[$id_i]['group_identifier'] = $i['group_identifier'];
	$items_arr[$id_i]['title'] = $i['title'];
	$items_arr[$id_i]['intro_text'] = $i['intro_text'];
	$items_arr[$id_i]['full_text'] = $i['full_text'];
	$items_arr[$id_i]['etext_enabled'] = $i['etext_enabled'];
	$items_arr[$id_i]['price'] = $i['price'];
	$items_arr[$id_i]['price_old'] = $i['price_old'];
	$items_arr[$id_i]['currency'] = $i['currency'];
	$items_arr[$id_i]['quantity'] = $i['quantity'];
	$items_arr[$id_i]['photo'] = $i['photo'];
	$items_arr[$id_i]['photo_big'] = $i['photo_big'];
	$items_arr[$id_i]['photo_more'] = $i['photo_more'];
	$items_arr[$id_i]['new'] = $i['new'];
	$items_arr[$id_i]['discount'] = $i['discount'];
	$items_arr[$id_i]['hit'] = $i['hit'];
	$items_arr[$id_i]['rating'] = $i['rating'];
	$items_arr[$id_i]['tag_title'] = $i['tag_title'];
	$items_arr[$id_i]['tag_description'] = $i['tag_description'];

	// Упрощённый вариант массива для javascript
	$items_arr_js[$id_i]['id'] = $i['id'];
	$items_arr_js[$id_i]['title'] = $i['title'];
	$items_arr_js[$id_i]['price'] = $i['price'];
	$items_arr_js[$id_i]['price_old'] = $i['price_old'];
	$items_arr_js[$id_i]['currency'] = $i['currency'];
	$items_arr_js[$id_i]['quantity'] = $i['quantity'];
	$items_arr_js[$id_i]['photo'] = $i['photo'];
	$items_arr_js[$id_i]['new'] = $i['new'];
	$items_arr_js[$id_i]['discount'] = $i['discount'];
	$items_arr_js[$id_i]['hit'] = $i['hit'];
	$items_arr_js[$id_i]['rating'] = $i['rating'];


	// Характеристики
	$stmt_chars = $db->prepare('
		SELECT c.id, c.item_id, c.name_id, c.value, c.ordering, c.status, n.name, n.unit, n.type
		FROM com_shop_char c
		JOIN com_shop_char_name n ON n.`id` = c.name_id
		WHERE c.item_id = :item_id ORDER BY c.ordering
	');

	$stmt_chars->execute(array('item_id' => $id_i));

	$k = 0;
	while($m = $stmt_chars->fetch())
	{
		// Заносим всё в массив
		$name = $m['name'];
		$items_arr[$id_i]['char'][$name]['unit'] = $m['unit'];
		$items_arr[$id_i]['char'][$name]['type'] = $m['type'];
		$items_arr[$id_i]['char'][$name]['value'][] = $m['value'];
		$items_arr[$id_i]['char'][$name]['value_item'] = $m['value']; // характеристика данного товара для select

		// Упрощённый вариант массива для javascript
		$items_arr_js[$id_i]['char'][$name]['unit'] = $m['unit'];
		$items_arr_js[$id_i]['char'][$name]['type'] = $m['type'];
		$items_arr_js[$id_i]['char'][$name]['value'][] = $m['value'];


		// Первая характеристика - общая и суммарная у всех товаров
		// Название первой характеристики первого товара
		if($n == '0' && $k == '0')
		{
			$char_first_name = $name;
			$char_first_value_arr = array();
		}

		// К зачению общей (первой) характеристики прибавляем значения других товаров
		if($name == $char_first_name)
		{
			$char_first_value_arr[] .= $m['value'];
		}

		$k++;
	}
	$n++;
}

// Карточки товара для javascript
$items_out_js = array();



// $item['title'] = preg_replace("/\\\/", " ", $item['title']);
// $page_title = $item['title'];


/*
// Тег title
if($item['tag_title'] != '')
{
	$tag_title = $item['tag_title'];
}
else
{
	$tag_title = $item['title'];	
}

// Тег description
if(strlen($item['tag_description']) != 0)
{
	$tag_description = $item['tag_description'];
}
else
{
	$tag_description = $item['title'].'. '.LANG_SHOP_ITEM_PRICE.': '.$item['price'].' '.$shopSettings->currency.' '.shopSettings->intro_text;
	$tag_description = mb_substr($tag_description, 0, mb_strrpos(mb_substr($tag_description, 0, 200, 'utf-8'),' ','utf-8'),'utf-8'); // Обрезаем по пробелу.
}
*/

// По умолчанию - обычный вывод, без группировки, выводим до шаблона, что бы получить title
$item = $items_arr[$id];


// === ПОДКЛЮЧАЕМ ШАБЛОН ТОВАРА ===
if($shopSettings->view_item_card == 999){include_once($root."/tmp/shop/item/tmp.php");}
else{include_once($root."/components/shop/frontend/item/tmp/".$shopSettings->view_item_card."/tmp.php");}

if($frontend_edit == 1){$head->addFile('/components/shop/frontend/item/edit.js');}
if($shopSettings->view_item_card == 999){$head->addFile('/tmp/shop/item/style.css');}
else{$head->addFile('/components/shop/frontend/item/tmp/'.$shopSettings->view_item_card.'/style.css');}


// Если есть группировка - делаем перебор всех товаров и вносим в JSON
if($shopSettings->grouping == 1 || $shopSettings->view_item_card == 4 || $shopSettings->view_item_card == 5)
{
	foreach($items_arr as $_id => $_item)
	{
		// Если включена группировка - все первые характеристики меняем на сводные, общие для всех
		if (isset($_item['char']))
		{
			$_item['char'][$char_first_name]['value'] = array_unique($char_first_value_arr);
		}

		$items_out_js[$_id] = shop_item_tmp($_item);

		// Если это текущий товар - определяем его
		if($_id == $id){$item = $_item;}
	}
}

// если товаров нет
if ($stmt_items->rowCount() == "0")
{
	// выдаём страницу ошибки 404.html
	header("HTTP/1.0 404 Not Found");
	include("404.php");
	exit;
}



if($shopSettings->grouping == 1 || $shopSettings->view_item_card == 4 || $shopSettings->view_item_card == 5)
{
	$head->addFile('/components/shop/frontend/item/items_group.js');
	$head->addCode('
		<script type="text/javascript">
		function items_obj_fn()
		{
			items_obj = eval('.json_encode($items_arr_js).');
			items_out = eval('.json_encode($items_out_js).');
		}
		items_obj_fn();
		</script>
	');
}

/*
echo '<pre>';
print_r($items_arr_js);
echo '</pre>';
*/



// --- Сопутствующие товары ---

$stmt_related = $db->prepare("
SELECT r.id, r.related_id, i.title, i.quantity, i.photo, i.new, i.discount, i.hit, i.rating,
CASE i.currency
WHEN '0' THEN i.price
WHEN '1' THEN i.price * ".CCurrency::getUSD()."
WHEN '2' THEN i.price * ".CCurrency::getEUR()."
WHEN '3' THEN i.price * ".$ue."
END as price,
CASE i.currency
WHEN '0' THEN i.price_old
WHEN '1' THEN i.price_old * ".CCurrency::getUSD()."
WHEN '2' THEN i.price_old * ".CCurrency::getEUR()."
WHEN '3' THEN i.price * ".$ue."
END as price_old
FROM com_shop_related_item r
JOIN com_shop_item i
ON i.id = r.related_id
WHERE r.item_id = :item_id
ORDER BY r.ordering
");

$stmt_related->execute(array('item_id' => $id));

$shop_related_items = '';
$shop_related_items_out = '';

if($stmt_related->rowCount() > 0)
{
	$shop_related_arr = $stmt_related->fetchAll();

	foreach($shop_related_arr as $r)
	{
		if($shopSettings->basket_type == 1){$click = 'onclick="shop_buy_fly('.$r['related_id'].');"';}
		else{$click = 'onclick="shop_buy('.$r['related_id'].');"';}

		if($r['photo'] != '' && is_file($root."/components/shop/photo/".$r['photo']))
		{
			$related_photo_out ='<img id="shop_item_img_'.$r['related_id'].'" border="0" alt="'.$r['title'].'" src="/components/shop/photo/'.$r['photo'].'">';
		}
		else
		{
			$related_photo_out = '<div id="shop_item_img_'.$r['related_id'].'" class="no-photo" style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;"></div>';
		}

		$new_out = '';
		$hit_out = '';
		$order_out = '';

		if($r['new'] == 1) $new_out = '<a class="related_sticker_new" href="/shop/item/'.$r['id'].'">'.$shopSettings->sticker_new.'</a>';
		if($r['hit'] == 1) $hit_out = '<a class="related_sticker_hit" href="/shop/item/'.$r['id'].'">'.$shopSettings->sticker_hit.'</a>';

		if($r['discount'] == 1)
		{
			if($r['price'] > 999999999) $r['price'] = 0;

			if($r['price'] > 0)
			{
				$r['price'] = number_format($r['price'], 0, '', ' ');
				$r['price_old'] = number_format($r['price_old'], 0, '', ' ');
				$discount_out = '<a class="related_sticker_sale" href="/shop/item/'.$r['id'].'">'.$shopSettings->sticker_sale.'</a>';
				$price_old_out = '<span class="related_item_price_old">'.$r['price_old'].'</span>';
				$price_out = '<span class="related_item_price_discount"><span>'.$r['price'].'</span><span class="related_item_rub_discount"> '.$shopSettings->currency.'</span></span>';
			}
			else
			{
				$discount_out = '';
				$price_old_out = '';
				$price_out = '';
			}
		}
		else
		{
			$discount_out = '';
			$price_old_out = '';

			if($r['price'] > 0)
			{
				$r['price'] = number_format($r['price'], 0, '', ' ');
				$r['price_old'] = number_format($r['price_old'], 0, '', ' ');
				$price_out = '<span>'.$r['price'].'</span><span class="related_item_rub"> '.$shopSettings->currency.'</span>';
			}
			else {$price_out = '';}
		}

		if($r['quantity'] == '0' && $shopSettings->item_quantity != 0 && $shopSettings->grouping != '1'){$order_out = '<a class="related_sticker_order" href="/shop/item/'.$r['id'].'">'.$shopSettings->sticker_order.'</a>';}

		$shop_related_items .= '
		<div class="related_item" style="width:'.($shopSettings->x_small + 20).'px">
			<div class="related_stickers">
				'.$new_out.'
				'.$discount_out.'
				'.$hit_out.'
				'.$order_out.'
			</div>
			<div>
				<a target="_blank" href="/shop/item/'.$r['related_id'].'">'.$related_photo_out.'</a>
			</div>
			<a class="related_item_name" target="_blank" href="/shop/item/'.$r['related_id'].'">'.$r['title'].'</a>
			<div class="related_item_desc">
					<div class="related_item_price">'.$price_old_out.$price_out.'</div>
					<div class="related_item_buy">
						<div class="related_item_buy_button" '.$click.'>'.LANG_SHOP_ITEM_BUY.'</div>
					</div>
			</div>
		</div>';
	}

	$shop_related_items_out = '
		<div class="related_items">
			<h3 class="related_item_title">'.$shopSettings->related_items.'</h3>
			'.$shop_related_items.'
		</div>
	';
}


// ####### Вывод товара ###############################################################
function component()
{
	global $domain, $root, $db, $item, $shopSettings, $shop_related_items_out, $frontend_edit;

	echo '<div id="component">'.shop_item_tmp($item).'</div>';
} // конец функции component


// ================================================================================================

// подключение модального окна "Задать вопрос по этому товару"
if(Settings::instance()->getValue('personal_information') == 1)
{
	$personal_information = '<div class="shop_item_question_personal"><br /><input required checked title="Вы должны дать согласие перед отправкой" type="checkbox"> Я согласен на <a href="/personal-information" target="_blank">обработку персональных данных</a></div>';
}
else{$personal_information = '';}

$head->addCode('
	<script type="text/javascript">
	var item_question = \'<form method="post" action="/shop/question" class="shop_item_question_main">\';
	item_question += \'<div class="shop_item_question_form">\';
	item_question += \'<div class="shop_item_question_title">'.LANG_SHOP_ITEM_ASK_ABOUT_PRODUCT.':</div>\';
	item_question += \'<div class="shop_item_question_name">\';
	item_question += \''.$item['title'].'<input type="hidden" name="item_title" value="'.$item['title'].'"><input type="hidden" name="item_id" value="'.$item['id'].'">\';
	item_question += \'<input type="hidden" name="item_id" value="'.$item['id'].'">\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<input type="email" name="email" size="20" placeholder="'.LANG_SHOP_ITEM_YOUR_EMAIL.'" class="input" autocomplete="off" maxlength="30" required title="'.LANG_SHOP_ITEM_INVALID_EMAIL.'">\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<textarea name="question" rows="3" placeholder="'.LANG_SHOP_ITEM_YOUR_QUASTION.'" class="input" ></textarea>\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<img class="shop_item_captcha" src="/administrator/captcha/pic.php?'.$rnd.'">\';
	item_question += \'<input type="text" name="captcha" size="4" class="shop_item_question_element shop_item_captcha_input input" autocomplete="off" maxlength="4" required pattern="[0-9]{4}" title="'.LANG_SHOP_ITEM_CAPTHA_CODE.'">\';
	item_question += \'<span class="shop_item_captcha_text">'.LANG_SHOP_ITEM_CAPTHA_CODE_2.'</span>\';
	item_question += \'<br style="clear: both;" />\';
	item_question += \'</div>\';
	item_question += \'<div class="shop_item_question_cont">\';
	item_question += \'<input type="submit" value="'.LANG_SHOP_ITEM_SEND.'" name="send" class="shop_item_button">\';
	item_question += \'</div>\';
	item_question += \''.$personal_information.'\';
	item_question += \'</div>\';
	item_question += \'</form>\';
	</script>
'
);



?>
