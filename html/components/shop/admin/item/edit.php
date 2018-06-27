<?php
// Редактируем страницу
defined('AUTH') or die('Restricted access');

include_once($root.'/lib/currency.php');

include_once($root."/components/shop/classes/classFilter.php");
include_once($root."/components/shop/classes/Chars.php");
include_once($root."/components/shop/admin/classes/CharsAdmin.php");

CCurrency::update();

// определяем id раздела
$item_id = intval($admin_d5);

$filter = new classFilter;
$chars = new CharsAdmin($item_id);

$head->addFile('/js/drag_drop/drag_drop.js');
$head->addFile('/js/drag_drop/drag_drop.css');
$head->addFile('/components/shop/admin/item/edit.css');
$head->addFile('/components/shop/admin/item/edit.js');


$head->addCode('
	<script type="text/javascript">

		var select = \''.$filter->getSelect().'\';

		var req = getXmlHttp();

		function url_ajax()
		{
			sef = document.getElementById("sef").value;

			var req = getXmlHttp()
			req.onreadystatechange = function()
			{
				if (req.readyState == 4)
				{
					if(req.status == 200)
					{
						document.getElementById("url_status").innerHTML = req.responseText;
					}
				}
			}
			req.open(\'GET\', \'/administrator/url/ajax.php?sef=\' + sef, true);
			req.send(null);
			document.getElementById("url_status").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
		}


		function img_ajax(files)
		{
			if (!files[0].type.match(/image.*/))
			{
				alert("Данный формат файла не поддерживается");
				return true;
			}

			var reader = new FileReader();

			reader.onload = function(read_src)
			{
				img_src = read_src.target.result;

				img_uri = encodeURIComponent(img_src);

				req.open("POST", "/admin/com/shop/img_upload_ajax", true);
				req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				req.send("act=upload&id='.$item_id.'&img_src="+img_uri);

				req.onreadystatechange = function()
				{
					if (req.readyState == 4)
					{
						if (req.status == 200)
						{
							var data = eval("(" + req.responseText + ")");
							document.getElementById("drag_trg").innerHTML += "<img class=\"drag_drop\" style=\"width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;\" src=\"/components/shop/photo/" + data.img_small + "\">";
							document.getElementById("images_order").value += data.img_small + ";";
							document.getElementById("img_status").innerHTML = "";

							// инициализируем заново функцию drag_drop - т.к. появился новый узел на котором следует отслеживать событие
							drag_drop("drag_trg", "drag_drop");

							// инициализируем контекстное меню
							contextmenu("drag_drop", contextmenu_item_photo);
						}
					}
				}

			}

			reader.readAsDataURL(files[0]);
			document.getElementById("img_status").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
		}
		

		function f_related_item(_related_item_id)
		{
			closedit_modal();
			
			var req = getXmlHttp()  
			req.onreadystatechange = function() 
			{
				if (req.readyState == 4) 
				{
					if(req.status == 200) 
					{
						var data = eval("(" + req.responseText + ")");

						if(data.photo == "")
						{
							var img_out = \'<div class="no-photo" style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;"></div>\';
						}
						else
						{
							var img_out = \'<img style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;" src="/components/shop/photo/\' + data.photo + \'"  draggable="false">\';
						}						

						document.getElementById("related_items").innerHTML += \'<div class="related_item" data-id="\' + data.id + \'" draggable="true"><div>\' + img_out + \'</div><div class="related_title" style="width:'.$shopSettings->x_small.'px;">\' + data.title + \'</div><div><span class="related_item_price">\' + data.price + \'</span> '.$shopSettings->currency.'</div></div>\';
						
						// инициализируем заново функцию drag_drop - т.к. появился новый узел на котором следует отслеживать событие
						drag_drop("related_items", "related_item");

						// инициализируем контекстное меню
						contextmenu("related_item", contextmenu_item_related);

						// добавляем товар в input
						document.getElementById("related_order").value += data.id + ";";						
					}
				}
			
			}
			req.open("GET", "/admin/com/shop/item/related_item/'.$item_id.'/" + _related_item_id, true);
			req.send(null);
			//document.getElementById("related_items").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
		}

	</script>
');



function a_com()
{
	global $db, $root, $domain, $url_arr, $item_id, $shopSettings, $chars;

	$h_img = $shopSettings->y_small;
	if($h_img < 50){$h_img = 50;}	// если высота меньше 50px, то высота = 50px, иначе = высоте картинки

	$stmt_item = $db->prepare('SELECT * FROM com_shop_item WHERE id = :id LIMIT 1');
	$stmt_item->execute(array('id' => $item_id));
	$item = $stmt_item->fetch();

	$currency_selected_0 = '';
	$currency_selected_1 = '';
	$currency_selected_2 = '';
	$currency_selected_3 = '';	

	if($item['currency'] == CURRENCY_RUB){$currency_selected_0 = 'selected="selected"';}
	if($item['currency'] == CURRENCY_USD){$currency_selected_1 = 'selected="selected"';}
	if($item['currency'] == CURRENCY_EUR){$currency_selected_2 = 'selected="selected"';}
	if($item['currency'] == 3){$currency_selected_3 = 'selected="selected"';}

	$rub_arr = array('р', 'р.', 'Р', 'Р.', 'руб', 'руб.', 'рублей', 'рублей.');
	if(in_array($shopSettings->currency, $rub_arr)) // Если указан рубль - выводим ещё вариант - в рублях по курсу.
	{
		$currency_out =
		'
		<select name="currency">
			<option value="'.CURRENCY_RUB.'" '.$currency_selected_0.'>'.$shopSettings->currency.'</option>
			<option value="'.CURRENCY_USD.'" '.$currency_selected_1.'>в рублях по курсу USD</option>
			<option value="'.CURRENCY_EUR.'" '.$currency_selected_2.'>в рублях по курсу EUR</option>
			<option value="3" '.$currency_selected_3.'>в рублях по внутреннему курсу у.е.</option>
		</select>
		';
	}
	else
	{
		$currency_out = $shopSettings->currency;
	}


	// --- ТИПЫ ЦЕН ---
	$stmt_price_type = $db->prepare("SELECT pt.id, pt.name, pi.price FROM com_shop_price_type pt LEFT JOIN com_shop_price_item pi ON pi.price_type_id = pt.id AND pi.item_id = :item_id");
	$stmt_price_type->execute(array('item_id' => $item_id));	

	if($stmt_price_type->rowCount() > 0)
	{
		$price_type_out = '<tr><td style="height:25px;" colspan="3">&nbsp;</td></tr><tr><td>&nbsp;</td><td style="height:25px;" colspan="2"><b>ТИПЫ ЦЕН ДЛЯ ЗАРЕГИСТРИРОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ</b></td></tr>';
		while($pt = $stmt_price_type->fetch())
		{
			$price_type_out .= '<tr><td>&nbsp;</td><td style="height:25px;">'.$pt['name'].'</td><td><input type="text" name="price_user['.$pt['id'].']" size="10" value="'.$pt['price'].'"></td></tr>';
		}
		$price_type_out .= '<tr><td style="height:25px;" colspan="3">&nbsp;</td></tr>';		
	}
	else
	{
		$price_type_out = '';		
	}



	// убираем у поля количество два нуля, после запятой для целочисленных значений
	if($item['quantity'] - ceil($item['quantity']) == 0){$item['quantity'] = intval($item['quantity']);}

	$form_action_out = '<form enctype="multipart/form-data" method="POST" action="/admin/com/shop/item/update/'.$item['id'].'/">';

	$chars_out = $chars->getTemplate($item['id']);

	if($shopSettings->item_quantity == 1 || $shopSettings->item_quantity == 2)
	{
		$item_quantity_out = '
		<tr>
			<td>&nbsp;</td>
			<td style="height:25px;">Количество <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Количество товаров</em>Товары с нолевым количеством обозначаются статусом <br><b>&quot;Под заказ&quot;</b>.</span></div></td>
			<td><input type="text" name="quantity" value="'.$item['quantity'].'" size="10"></td>
		</tr>';
	}
	else
	{
		$item_quantity_out = '';
	}


	// Условия
	if($item['new'] === "1"){$pub_new = "checked";} else {$pub_new = "";}
	if($item['discount'] === "1"){$pub_discount = "checked";} else {$pub_discount = "";}
	if($item['hit'] === "1"){$pub_hit = "checked";} else {$pub_hit = "";}	
	if($item['pub'] === "1"){$pub_checked = "checked";} else {$pub_checked = "";}
	if($item['etext_enabled'] === "1"){$etext_enabled_checked = "checked";} else {$etext_enabled_checked = "";}
	if($item['photo'] == '')
	{
		$img = '';
		$img_more_input = '';
	}
	else
	{
		$img = '<img class="drag_drop" style="width:'.$shopSettings->x_small.'px; height:'.$h_img.'px;" src="/components/shop/photo/'.$item['photo'].'">';
		$img_more_input = $item['photo'].';';
	}


	if ($item['photo_more'] == '')
	{
		$img_more_out = '';
	}
	else
	{
		$photo_arr = explode(';', $item['photo_more']);
		$img_more_out = '';

		if(count($photo_arr) >= 1)
		{
			for($i=0; $i<count($photo_arr)-1; $i++)
			{
				$img_more_out .= '<img class="drag_drop" style="width:'.$shopSettings->x_small.'px; height:'.$h_img.'px;" src="/components/shop/photo/'.$photo_arr[$i].'" alt="">';
				$img_more_input .= $photo_arr[$i].';';
			}
		}
	}
	
	$stmt_related = $db->prepare("
	SELECT r.id, r.related_id, i.title, i.photo,
	CASE i.currency
	WHEN '0' THEN i.price
	WHEN '1' THEN i.price * ".CCurrency::getUSD()."
	WHEN '2' THEN i.price * ".CCurrency::getEUR()."
	END as price
	FROM com_shop_related_item r
	JOIN com_shop_item i
	ON i.id = r.related_id
	WHERE r.item_id = :item_id 
	ORDER BY r.ordering
	");
	
	$stmt_related->execute(array('item_id' => $item_id));

	$related_items_input = '';
	$related_items_out = '';
	
	if($stmt_related->rowCount() > 0)
	{
		while($r = $stmt_related->fetch())
		{
			if($r['price'] > 999999999) $r['price'] = 0;
			$r['price'] = number_format($r['price'], 0, '', ' ');

			if($r['photo'] == '')
			{
				$photo_out = '<div id="shop_item_img_'.$r['related_id'].'" class="no-photo" style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;"></div>';
			}
			else
			{
				$photo_out = '<img style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;" src="/components/shop/photo/'.$r['photo'].'" draggable="false">';
			}
			
			$related_items_input .= $r['id'].';';
			$related_items_out .= '
			<div class="related_item" data-id="'.$r['id'].'" draggable="true">
				<div>'.$photo_out.'</div>
				<div class="related_title" style="width:'.$shopSettings->x_small.'px;">'.$r['title'].'</div>
				<div><span class="related_item_price">'.$r['price'].'</span> '.$shopSettings->currency.'</div>
			</div>';
		}
	}

	include_once($root."/components/shop/admin/item/tmp/tmp.php"); // шаблон вывода

} // конец функции

?>