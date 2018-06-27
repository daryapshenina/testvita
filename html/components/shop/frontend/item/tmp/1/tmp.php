<?php
// Расширенный - 1
defined('AUTH') or die('Restricted access');

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


function shop_item_tmp($item)
{
	global $root, $domain, $shopSettings, $price_type, $shop_related_items_out, $frontend_edit;

	$item['title'] = preg_replace("/\\\/", " ", $item['title']);
	$item['price'] = number_format($item['price'], 0, '', ' ');
	$item['price_old'] = number_format($item['price_old'], 0, '', ' ');

	if($item['quantity'] == '0' && $shopSettings->item_quantity != 0){$quantity_status = '<div class="shop_item_qs">'.LANG_SHOP_ITEM_UNDER_ORDER.'</div>';}else{$quantity_status = '';}
	if ($shopSettings->question){$question_out = '<a href="#" onclick="DAN.modal.add(item_question, \'450\', \'0\')" class="shop_item_question">'.LANG_SHOP_ITEM_ASK.'</a>';} else{{$question_out = '';}}

	$img_more_out = '';

	if($item['photo_big'] == '' || !is_file($root."/components/shop/photo/".$item['photo_big']))
	{
		$photo_big ='<img class="item_photo_big" src="/components/shop/frontend/tmp/images/no-photo.png" alt="'.$item['title'].'" />';
	}
	else
	{
		$size = getimagesize($root.'/components/shop/photo/'.$item['photo_big']);
		if($size[0] > 400){$size[0] = 400;}
		if(MobileDetector::getDevice() == NULL){$show = 'show';}else{$show = '';}
		$photo_big ='<img class="'.$show.' item_photo_big" style="max-width:'.$size[0].'px;" src="/components/shop/photo/'.$item['photo_big'].'" alt="'.$item['title'].'" itemprop="image" />';

		if($item['photo_more'] != '')
		{
			$photo_arr = explode(';', $item['photo_more']);

			if(count($photo_arr) >= 1)
			{
				for($i=0; $i<count($photo_arr)-1; $i++)
				{
					$photo_arr_big[$i] = str_replace('.jpg', '_.jpg', $photo_arr[$i]);
					$img_more_out .= '
						<a class="show" style="margin:0px 10px 10px 0px;" href="/components/shop/photo/'.$photo_arr_big[$i].'">
							<img class="item_photo_more" src="/components/shop/photo/'.$photo_arr[$i].'" alt="">
						</a>
					';
				}
			}
		}

		$img_more_out = '<div>'.$img_more_out.'</div>';
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
	if($item['new'] == 1){$new_out = '<div class="item_sticker_new"></div>';}
	else{$new_out = '';}

	if($item['hit'] == 1){$hit_out = '<div class="item_sticker_hit"></div>';}
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
		$quantity_out = '<div class="item_quantity_out"></div>';
	}
	else
	{
		$quantity_out = '';
	}


	if($item['discount'] == 1)
	{
		$discount_out = '<div class="item_sticker_discount"></div>';
		$price = '<span class="item_price_discount">&nbsp;'.$item['price'].'&nbsp;</span><span class="item_rub"> '.$shopSettings->currency.'</span>';
		$price_old = '<span class="item_price_old">&nbsp;'.$item['price_old'].'&nbsp;</span>';
	}
	else
	{
		$discount_out = '';
		$price = '<span class="item_block_price" itemprop="price">'.$item['price'].'</span><span class="item_block_rub" itemprop="priceCurrency"> '.$shopSettings->currency.'</span>';
		$price_old = '';
	}


	if($item['price'] != 0){$price_out = '<div class="item_price_out" itemscope itemtype="http://schema.org/Offer">'.$price_old.$price.'</div>';}
	else{$price_out = '';}


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


	$out = '
		<form'.$edit_data.' class="'.$edit_class.'shop_item_container" method="POST" action="/shop/basket">
			<div itemscope itemtype="http://schema.org/Product">
				'.$price_type.'
				<h1 class="title" itemprop="name">'.$item['title'].'</h1>
				<div id="item_main" class="item_main">
					<div id="item_photo_container">
						<div class="item_photo_container_border">
							'.$new_out.'
							'.$discount_out.'
							'.$quantity_out.'
							'.$hit_out.'
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
							<div class="shop-but" ><input type="submit" value="'.$shopSettings->sticker_add_to_cart.'" class="item_but" name="shopbutton" /></div>
							'.$question_out.'
						</div>
					</div>
				</div>

				<input id="item_id" type="hidden" value="'.$item['id'].'" name="item_id" />
			</div>

			<div class="item_gallery">'.$img_more_out.'</div>
			<div>&nbsp;</div>
			<div itemprop="description" class="item_full_text">'.$item['full_text'].'</div>
			'.$shop_related_items_out.'
		</form>
	';

	return $out;
}

?>