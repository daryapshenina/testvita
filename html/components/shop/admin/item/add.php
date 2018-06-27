<?php
// Добавляем новую страницу
defined('AUTH') or die('Restricted access');

include_once($root.'/lib/currency.php');
include_once($root."/components/shop/classes/classFilter.php");

// Характеристики
$filter = new classFilter;

$head->addFile('/js/drag_drop/drag_drop.js');
$head->addFile('/components/shop/admin/item/edit.css');
$head->addFile('/components/shop/admin/item/edit.js');

// Скрипт для получения списка доступных характеристик выбранного раздела
$head->addCode('
	<script type="text/javascript">

		select = \''.$filter->getSelect().'\';

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
						document.getElementById("related_items").innerHTML += "<div class=\"related_item\" data-id=\"" + data.id + "\" draggable=\"true\"><div><img style=\"width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;\" src=\"/components/shop/photo/" + data.photo + "\"  draggable=\"false\"></div><div>" + data.title + "</div><div><span class=\"related_item_price\">" + data.price + "</span> '.$shopSettings->currency.'</div></div>";
						
						// инициализируем заново функцию drag_drop - т.к. появился новый узел на котором следует отслеживать событие
						drag_drop("related_items", "related_item");

						// инициализируем контекстное меню
						contextmenu("related_item", contextmenu_item_related);

						// добавляем товар в input
						document.getElementById("related_order").value += data.id + ";";
					}
				}
			
			}
			req.open("GET", "/admin/com/shop/item/related_item//" + _related_item_id, true);
			req.send(null);
		}

	</script>
');



// Перед тем как добавить товар - проверяем - есть ли разделы
$stmt_section = $db->query('SELECT id FROM com_shop_section');
if($stmt_section->rowCount() == 0)
{
	function a_com()
	{
		echo
		'
		<div id="main-top">Отсутствуют разделы</div>
		<div style="padding: 10px">Отсутствуют разделы. Необходимо завести хотя бы один раздел.</div>
		';
	}
}
else
{
	function a_com()
	{
		global $db, $root, $domain, $admin_d5, $url_arr, $item_id, $shopSettings, $chars;
		
		$refer_arr = explode('/', $_SERVER['HTTP_REFERER']);		
		
		$item['id'] = 0;		
		$item['identifier'] = '';
		$item['group_identifier'] = '';		
		if(isset($refer_arr['6']) && $refer_arr['6'] == 'section'){$item['section'] = $refer_arr['7'];} else {$item['section'] = intval($admin_d5);} // определяем раздел в зависиммости от того, откуда перешли
		$item['title'] = '';		
		$item['price'] = '';
		$item['price_old'] = '';
		$item['etext'] = '';
		$item['tag_title'] = '';
		$item['tag_description'] = '';
		$item['intro_text'] = '';
		$item['full_text'] = '';
		$item['rating'] = '';
		$pub_checked = 'checked';		
		$pub_discount = '';
		$pub_new = '';
		$pub_hit = '';
		$img = '';
		$img_more_out ='';
		$currency_selected_0 = '';
		$currency_selected_1 = '';
		$currency_selected_2 = '';
		$currency_selected_3 = '';
		$chars_out = '';
		$etext_enabled_checked = '';

		$h_img = $shopSettings->y_small;
		if($h_img < 50){$h_img = 50;}	// если высота меньше 50px, то высота = 50px, иначе = высоте картинки
		
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
		$stmt_price_type = $db->prepare("SELECT id, name FROM com_shop_price_type");
		$stmt_price_type->execute(array('item_id' => $item_id));	

		if($stmt_price_type->rowCount() > 0)
		{
			$price_type_out = '<tr><td style="height:25px;" colspan="3">&nbsp;</td></tr><tr><td>&nbsp;</td><td style="height:25px;" colspan="2"><b>ТИПЫ ЦЕН ДЛЯ ЗАРЕГИСТРИРОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ</b></td></tr>';
			while($pt = $stmt_price_type->fetch())
			{
				$price_type_out .= '<tr><td>&nbsp;</td><td style="height:25px;">'.$pt['name'].'</td><td><input type="text" name="price_user['.$pt['id'].']" size="10" value="" required=""></td></tr>';
			}
			$price_type_out .= '<tr><td style="height:25px;" colspan="3">&nbsp;</td></tr>';		
		}
		else
		{
			$price_type_out = '';		
		}


		$form_action_out = '<form enctype="multipart/form-data" method="POST" action="/admin/com/shop/item/insert">';		
		
		if($shopSettings->item_quantity == 1 || $shopSettings->item_quantity == 2)
		{
			$item_quantity_out = '
			<tr>
				<td>&nbsp;</td>
				<td style="height:25px;">Количество <div class="help"><span class="tooltip"><img src="/administrator/tmp/images/question-50.png" alt="Помощь" /><em>Количество товаров</em>Товары с нолевым количеством обозначаются статусом <br><b>&quot;Под заказ&quot;</b>.</span></div></td>
				<td><input type="text" name="quantity" value="1" size="10"></td>
			</tr>';
		}
		else
		{
			$item_quantity_out = '';
		}
		
		$related_items_out = '';
		$related_items_input = '';		

		include_once($root."/components/shop/admin/item/tmp/tmp.php"); // шаблон вывода
	} // конец функции
}

?>