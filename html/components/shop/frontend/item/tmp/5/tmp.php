<?php
// Расширенный - 2
defined('AUTH') or die('Restricted access');

include_once $root."/classes/MobileDetector.php";
$head->addFile("/components/shop/frontend/item/tmp/".$shopSettings->view_item_card."/tmp.js");


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
	$tag_description = $item['title'].'. '.LANG_SHOP_ITEM_PRICE.': '.$item['price'].' '.$shopSettings->currency.' '.$item['intro_text'];
	$tag_description = mb_substr($tag_description, 0, mb_strrpos(mb_substr($tag_description, 0, 200, 'utf-8'),' ','utf-8'),'utf-8'); // Обрезаем по пробелу.
}


function shop_item_tmp($_item)
{
	global $root, $domain, $shopSettings, $price_type, $items_arr, $shop_related_items_out, $frontend_edit;

	$_item['title'] = preg_replace("/\\\/", " ", $_item['title']);
	$_item['price'] = number_format($_item['price'], 0, '', ' ');
	$_item['price_old'] = number_format($_item['price_old'], 0, '', ' ');

	if($_item['quantity'] == '0' && $shopSettings->item_quantity != 0){$quantity_status = '<div class="shop_item_qs">'.LANG_SHOP_ITEM_UNDER_ORDER.'</div>';}else{$quantity_status = '';}
	if ($shopSettings->question){$question_out = '<a href="#" onclick="DAN.modal.add(item_question, \'450\', \'0\')" class="shop_item_question">'.LANG_SHOP_ITEM_ASK.'</a>';} else{{$question_out = '';}}

	$img_more_out = '';

	if($_item['photo_big'] == '' || !is_file($root."/components/shop/photo/".$_item['photo_big']))
	{
		$photo_big ='<img id="shop_item_img_'.$_item['id'].'" class="item_photo_big" src="/components/shop/frontend/tmp/images/no-photo.png" alt="'.$_item['title'].'"/>';
	}
	else
	{
		$size = getimagesize($root.'/components/shop/photo/'.$_item['photo_big']);
		if($size[0] > 400){$size[0] = 400;}
		if(MobileDetector::getDevice() == NULL){$show = 'show';}else{$show = '';}
		$photo_big ='<img id="shop_item_img_'.$_item['id'].'" id="item_photo" class="'.$show.' item_photo_big" style="max-width:'.$size[0].'px;" src="/components/shop/photo/'.$_item['photo_big'].'" alt="'.$_item['title'].'" itemprop="image"/>';

		if($_item['photo_more'] != '')
		{
			$photo_arr = explode(';', $_item['photo_more']);

			if(count($photo_arr) >= 1)
			{
				for($i=0; $i<count($photo_arr)-1; $i++)
				{
					$photo_arr_big[$i] = str_replace('.jpg', '_.jpg', $photo_arr[$i]);
					$img_more_out .= '<img class="show item_photo_more" src="/components/shop/photo/'.$photo_arr_big[$i].'" alt="">';
				}
			}
		}

		$img_more_out = '<div id="item_photo_more">'.$img_more_out.'</div>';
	}



	$img_small_item = '';

	// МАЛЫЕ ИЗОБРАЖЕНИЯ, ПО КОТОРЫМ ИДЁТ ПЕРЕКЛЮЧЕНИЕ
	foreach($items_arr as $_id => $_i)
	{
		if($_i['photo'] == '' || !is_file($root."/components/shop/photo/".$_i['photo']))
		{
			$photo_small ='<img class="img_small_item_img" src="/components/shop/frontend/tmp/images/no-photo.png" alt="'.$_i['title'].'"/>';
		}
		else
		{
			$photo_small = '<img class="img_small_item_img" src="/components/shop/photo/'.$_i['photo'].'" alt="'.$_i['title'].'" />';
		}

		if($_item['id'] == $_i['id']){$img_small_selected = 'img_small_item_selected';}else{$img_small_selected = '';}
		$img_small_item .= '<div onclick="items_tmp_img_small(\''.$_i['id'].'\');" class="img_small_item '.$img_small_selected.'">'.$photo_small.'</div>';
	}

	if(count($items_arr) > 1){$img_small_out = '<div class="img_small_container"><div class="img_small_title">Выбрать:</div>'.$img_small_item.'</div>';}else{$img_small_out = '';}


	if($_item['intro_text'] != '')
	{
		$intro_text_out = '<div class="item_intro_text" itemprop="description">'.$_item['intro_text'].'</div>';
	}
	else
	{
		$intro_text_out = '';
	}

	$item_char = '';
	$item_char_tr = '';
	$i = 0;


	if(isset($_item['char']))
	{
		foreach ($_item['char'] as $name => $char)
		{
			if(count($char['value']) > 1)
			{
				$char['value'] = array_unique($char['value']);
				asort($char['value']);

				$onchange = '';
				if ($i == '0' && ($shopSettings->grouping == 1 || $shopSettings->view_item_card == 4 || $shopSettings->view_item_card == 5)) // Первая характеристика + включена группировка
				{
					$onchange = 'id="char_sel" onchange=items_tmp_char();';
				}

				$value_out = '';


				$value_out = '<select class="char_select" '.$onchange.' name="char['.$name.']">';

				// перебираем характеристику
				foreach ($char['value'] as $option_value)
				{
					 // Первая характеристика и первое значение неотсортированного списка - selected
					if($i == '0' && $option_value == $_item['char'][$name]['value_item']){$selected = 'selected';}else{$selected = '';}
					$value_out .= "<option ".$selected.">".$option_value."</option>\n";
				}
				$value_out .= '</select>';
			}
			else
			{
				$value_out = $char['value'][0];
			}

			if(!isset($char['unit']) || $char['unit'] == ''){$unit_out = '';} else {$unit_out = ', '.$char['unit'];}


			$item_char_tr .= '<tr>';
			$item_char_tr .= '<td class="item_char_td_1">'.$name.$unit_out.'</td>';
			$item_char_tr .= '<td class="item_char_td_2">'.$value_out.'</td>';
			$item_char_tr .= '</tr>';

			$i++;
		}
	}


	if($item_char_tr != ''){$item_char = '<div class="item_char_table_padding"><table class="item_char_table">'.$item_char_tr.'</table></div>';}

	if($_item['new'] == 1){$new_out = '<div class="item_sticker_new">'.$shopSettings->sticker_new.'</div>';}
	else{$new_out = '';}

	if($_item['hit'] == 1){$hit_out = '<div class="item_sticker_hit">'.$shopSettings->sticker_hit.'</div>';}
	else{$hit_out = '';}

	// Рейтинг
	if($_item['rating'] > 0)
	{
		$rating = round($_item['rating']);
		if($rating == 1) $rating_out = '<div class="item_rating">★☆☆☆☆</div>';
		if($rating == 2) $rating_out = '<div class="item_rating">★★☆☆☆</div>';
		if($rating == 3) $rating_out = '<div class="item_rating">★★★☆☆</div>';
		if($rating == 4) $rating_out = '<div class="item_rating">★★★★☆</div>';
		if($rating == 5) $rating_out = '<div class="item_rating">★★★★★</div>';
	}
	else{$rating_out = '';}

	if($_item['quantity'] == '0' && $shopSettings->item_quantity != 0)
	{
		$quantity_out = '<div class="item_sticker_order">'.$shopSettings->sticker_order.'</div>';
	}
	else
	{
		$quantity_out = '';
	}

	if($_item['discount'] == 1)
	{
		$discount_out = '<div class="item_sticker_discount">'.$shopSettings->sticker_sale.'</div>';
		$price = '<span class="item_price_discount">'.$_item['price'].'</span><span class="item_rub">'.$shopSettings->currency.'</span>';
		$price_old = '<span class="item_price_old">'.$_item['price_old'].'</span>';
	}
	else
	{
		$discount_out = '';
		$price = '<span class="item_price" itemprop="price">'.$_item['price'].'</span><span class="item_rub" itemprop="priceCurrency">'.$shopSettings->currency.'</span>';;
		$price_old = '';
	}


	if($_item['price'] != 0){$price_out = '<div class="item_price_out" itemscope itemtype="http://schema.org/Offer">'.$price_old.$price.'</div>';}
	else{$price_out = '';}

	// Временно поставим $shopSettings->checkout_type = 1; Когда переделаем $shopSettings и пропишем в БД - просто удалить эту строчку; Сделать в следующем обновлении!
	$shopSettings->checkout_type = 1;

	if($shopSettings->checkout_type == 0) // Стандартная форма оформления покупки
	{
		$form_1_out = '<form method="POST" action="/shop/basket">';
		$form_2_out = '</form>';
		$form_submit_out = '';
	}
	else // Летающая корзина
	{
		$form_1_out = '';
		$form_2_out = '';

		if($shopSettings->basket_type == 1) // Летающая корзина
		{
			$form_submit_out = 'onclick="shop_buy_fly('.$_item['id'].');"';
		}
		else
		{
			$form_submit_out = 'onclick="shop_buy('.$_item['id'].');"';
		}
	}


	// frontend редактирование
	if($frontend_edit == 1)
	{
		$edit_data = ' data-type="com_shop_item" data-id="'.$_item['id'].'"';
		$edit_class = 'edit_mode ';
	}
	else
	{
		$edit_data = '';
		$edit_class = '';
	}


	$out = $form_1_out.'
			<div'.$edit_data.' class="'.$edit_class.'shop_item_container" itemscope itemtype="http://schema.org/Product">
				'.$price_type.'
				<h1 id="item_title" class="title" itemprop="name">'.$_item['title'].'</h1>
				<div id="item_main" class="item_main">
					<div id="item_photo_container">
						<div class="item_photo_container_border">
							<div class="item_photo_container_content_float">
								'.$new_out.'
								'.$discount_out.'
								'.$hit_out.'
								'.$quantity_out.'
							</div>
							<div class="item_photo_container_content">
								'.$photo_big.'
							</div>
						</div>
					</div>
					<div id="iter_shortdesc_container">
						<div class="iter_shortdesc_container_padding">
							'.$rating_out.'
							'.$item_char.'
							'.$intro_text_out.'
							'.$price_out.'
							<div class="shop_quantity"><div id="quantity_minus" onclick="item_quantity('.$_item['id'].',-1);">-</div><input type="text" id="shop_item_num_'.$_item['id'].'" class="input_quantity" name="input_quantity" value="1" title="'.LANG_SHOP_ITEM_QUANTITY.'"><div id="quantity_plus" onclick="item_quantity('.$_item['id'].',1);">+</div></div>
							<div class="shop_but"><input '.$form_submit_out.' type="submit" value="'.$shopSettings->sticker_add_to_cart.'" class="but_cart" name="shopbutton" /></div>
							'.$question_out.'
						</div>
					</div>
				</div>
				<input id="item_id" type="hidden" value="'.$_item['id'].'" name="item_id" />
			'.$form_2_out.'
			'.$img_small_out.'
			'.$img_more_out.'
			<div id="item_full_text" class="item_full_text" itemprop="description">'.$_item['full_text'].'</div>
			'.$shop_related_items_out.'
		</div>
	';

	return $out;
}

?>