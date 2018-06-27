<?php
// Обои, плитка
defined('AUTH') or die('Restricted access');

include_once $root."/classes/MobileDetector.php";
$head->addFile("/components/shop/frontend/item/tmp/6/tmp.js");


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


function shop_item_tmp($item)
{
	global $root, $domain, $shopSettings, $shop_related_arr, $shop_related_items_out, $frontend_edit;

	$item['title'] = preg_replace("/\\\/", " ", $item['title']);
	$item['price'] = number_format($item['price'], 0, '', ' ');
	$item['price_old'] = number_format($item['price_old'], 0, '', ' ');

	if($item['quantity'] == '0' && $shopSettings->item_quantity != 0){$quantity_status = '<div class="shop_item_qs">'.LANG_SHOP_ITEM_UNDER_ORDER.'</div>';}else{$quantity_status = '';}
	if ($shopSettings->question){$question_out = '<a href="#" onclick="DAN.modal.add(item_question, \'450\', \'0\')" class="shop_item_question">'.LANG_SHOP_ITEM_ASK.'</a>';} else{{$question_out = '';}}

	$img_more_out = '';

	if($item['photo_big'] == '' || !is_file($root."/components/shop/photo/".$item['photo_big']))
	{
		$photo_big ='<img id="shop_item_img_'.$item['id'].'" src="/components/shop/frontend/tmp/images/no-photo.png" alt="'.$item['title'].'"/>';
	}
	else
	{
		$photo_big ='<img id="shop_item_img_'.$item['id'].'" class="item_photo_big"  src="/components/shop/photo/'.$item['photo_big'].'" alt="'.$item['title'].'" itemprop="image"/>';

		if($item['photo_more'] != '')
		{
			$photo_arr = explode(';', $item['photo_more']);

			if(count($photo_arr) >= 1)
			{
				for($i=0; $i<count($photo_arr)-1; $i++)
				{
					$photo_arr_big[$i] = str_replace('.jpg', '_.jpg', $photo_arr[$i]);
					$img_more_out .= '
						<a class="show item_photo_more_a" href="/components/shop/photo/'.$photo_arr_big[$i].'">
							<img class="item_photo_more" src="/components/shop/photo/'.$photo_arr[$i].'" alt="">
						</a>
					';
				}
			}
		}
		$img_more_out = '<div id="item_photo_more" class="item_gallery"><div class="item_gallery_title">Фото в интерьере: </div><div class="item_gallery_photo">'.$img_more_out.'</div></div>';
	}

	if($item['intro_text'] != '')
	{
		$intro_text_out = '<div class="item_intro_text" itemprop="description">'.$item['intro_text'].'</div>';
	}
	else
	{
		$intro_text_out = '';
	}

	$item_char = '';
	$item_char_tr = '';
	$i = 0;


	if(isset($item['char']))
	{
		foreach ($item['char'] as $name => $char)
		{
			if(count($char['value']) > 1)
			{
				$char['value'] = array_unique($char['value']);
				asort($char['value']);

				$onchange = '';
				if ($i == '0' && $shopSettings->grouping == 1) // Первая характеристика + включена группировка
				{
					$onchange = 'id="char_sel" onchange=items_tmp_char();';
				}

				$value_out = '<select '.$onchange.' name="char['.$name.']">';

				// перебираем характеристику
				foreach ($char['value'] as $otion_value)
				{
					 // Первая характеристика и первое значение неотсортированного списка - selected
					if($i == '0' && $otion_value == $item['char'][$name]['value_item']){$selected = 'selected';}else{$selected = '';}
					$value_out .= "<option ".$selected.">".$otion_value."</option>\n";
				}
				$value_out .= '</select>';
			}
			else
			{
				$value_out = $char['value'][0];
			}

			if($char['unit'] == ''){$unit_out = '';} else {$unit_out = ', '.$char['unit'];}

			$item_char_tr .= '<tr>';
			$item_char_tr .= '<td class="item_char_td_1">'.$name.$unit_out.'</td>';
			$item_char_tr .= '<td class="item_char_td_2">'.$value_out.'</td>';
			$item_char_tr .= '</tr>';

			$i++;
		}
	}


	if($item_char_tr != ''){$item_char = '<div class="item_char_table_padding"><table class="item_char_table">'.$item_char_tr.'</table></div>';}

	if($item['new'] == 1){$new_out = '<div class="item_sticker_new">'.$shopSettings->sticker_new.'</div>';}
	else{$new_out = '';}

	if($item['hit'] == 1){$hit_out = '<div class="item_sticker_hit">'.$shopSettings->sticker_hit.'</div>';}
	else{$hit_out = '';}

	// Рейтинг
	if($item['rating'] > 0)
	{
		$rating = round($item['rating']);
		if($rating == 1) $rating_out = '<div class="item_rating">★☆☆☆☆</div>';
		if($rating == 2) $rating_out = '<div class="item_rating">★★☆☆☆</div>';
		if($rating == 3) $rating_out = '<div class="item_rating">★★★☆☆</div>';
		if($rating == 4) $rating_out = '<div class="item_rating">★★★★☆</div>';
		if($rating == 5) $rating_out = '<div class="item_rating">★★★★★</div>';
	}
	else{$rating_out = '';}

	if($item['quantity'] == '0' && $shopSettings->item_quantity != 0)
	{
		$quantity_out = '<div class="item_sticker_order">'.$shopSettings->sticker_order.'</div>';
	}
	else
	{
		$quantity_out = '';
	}

	if($item['discount'] == 1)
	{
		$discount_out = '<div class="item_sticker_discount">'.$shopSettings->sticker_sale.'</div>';
		$price = '<span class="item_price_discount">'.$item['price'].'</span><span class="item_rub">'.$shopSettings->currency.'</span>';
		$price_old = '<span class="item_price_old">'.$item['price_old'].'</span>';
	}
	else
	{
		$discount_out = '';
		$price = '<span class="item_price" itemprop="price">'.$item['price'].'</span><span class="item_rub" itemprop="priceCurrency">'.$shopSettings->currency.'</span>';
		$price_old = '';
	}


	if($item['price'] != 0){$price_out = '<div class="item_price_out" itemscope itemtype="http://schema.org/Offer">'.$price_old.$price.'</div>';}
	else{$price_out = '';}



	// === ТОВАРЫ КОМПАНЬОНЫ ===
	if(count($shop_related_arr) > 0)
	{
		$comp_out = '';

		foreach($shop_related_arr as $c)
		{
			$c['price'] = number_format($c['price'], 0, '', ' ');
			$companions_photo = str_replace('.jpg', '_.jpg', $c['photo']);
			$comp_out .= '<img onclick="companions('.$c['related_id'].', \''.$c['title'].'\', \''.$companions_photo.'\', \''.$c['price'].'\', \''.$shopSettings->currency.'\')" class="item_companions_container_photo" src="/components/shop/photo/'.$c['photo'].'" alt="">';
		}

		$companions_out = '
		<div class="item_companions_container">
			<div class="item_companions_container_text">Компаньоны</div>
			<div class="item_companions_container_items">'.$comp_out.'</div>
		</div>';
	}
	else
	{
		$companions_out = '';
	}


	// Временно поставим $shopSettings->checkout_type = 0; Когда переделаем $shopSettings и пропишем в БД - просто удалить эту строчку; Сделать в следующем обновлении!
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
			$form_submit_out = 'onclick="shop_buy_fly('.$item['id'].');"';
		}
		else
		{
			$form_submit_out = 'onclick="shop_buy('.$item['id'].');"';
		}
	}


	// frontend редактирование
	if($frontend_edit == 1)
	{
		$edit_data = ' data-type="com_shop_item" data-id="'.$item['id'].'"';
		$edit_class = 'edit_mode ';
	}
	else
	{
		$edit_data = '';
		$edit_class = '';
	}


	$out = $form_1_out.'
		<div'.$edit_data.' class="'.$edit_class.'shop_item_container" itemscope temtype="http://schema.org/Product">
			<h1 id="item_title" class="title" itemprop="name">'.$item['title'].'</h1>
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
							'.$companions_out.'
						</div>
					</div>
				</div>
				<div id="item_shortdesc_container">
					<div class="item_shortdesc_container_padding">
						'.$rating_out.'
						'.$item_char.'
						'.$price_out.'
						<div class="shop_quantity"><div id="quantity_minus" onclick="item_quantity('.$item['id'].', -1)">-</div><input type="text" id="shop_item_num_'.$item['id'].'" class="input_quantity" name="input_quantity" value="1" title="'.LANG_SHOP_ITEM_QUANTITY.'"><div id="quantity_plus" onclick="item_quantity('.$item['id'].', 1)">+</div></div>
						<div class="shop_but" ><input '.$form_submit_out.' type="submit" value="'.$shopSettings->sticker_add_to_cart.'" class="button_cart" name="shopbutton" /></div>
						'.$question_out.'
						'.$img_more_out.'
						'.$intro_text_out.'
					</div>
				</div>
			</div>
			<input id="item_id" type="hidden" value="'.$item['id'].'" name="item_id" />
			'.$form_2_out.'

			<div>&nbsp;</div>
			<div id="item_full_text" class="item_full_text" itemprop="description">'.$item['full_text'].'</div>
		</div>
	';

	return $out;
}

?>