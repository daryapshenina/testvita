<?php
// Стандартный
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
		if($size[0] > 600 || $size[1] > 480)
		{
			$photo_big = '<img class="item_photo_big show" src="/components/shop/photo/'.$item['photo_big'].'" alt="'.$item['title'].'" itemprop="image" />';
		}
		else
		{
			$photo_big = '<img class="item_photo_big" src="/components/shop/photo/'.$item['photo_big'].'" alt="'.$item['title'].'" itemprop="image" />';
		}

		if ($item['photo_more'] != '')
		{
			$photo_arr = explode(';', $item['photo_more']);

			if(count($photo_arr) >= 1)
			{
				$img_more_out .= '<div class="item_photo_more_container">';
				for($i=0; $i<count($photo_arr)-1; $i++)
				{
					$photo_arr_big[$i] = str_replace('.jpg', '_.jpg', $photo_arr[$i]);
					$img_more_out .= '<img class="show2 item_photo_more" style="width:100px;" src="/components/shop/photo/'.$photo_arr[$i].'" alt="" longdesc="/components/shop/photo/'.$photo_arr_big[$i].'">';
				}
				$img_more_out .= '</div>';
			}
		}

	} // если есть изображение



	// Скидка
	if($item['discount'] == 1)
	{
		$discount_out = '<a class="item_sticker_sale" href="/shop/item/'.$item['id'].'"></a>';
		$price_old_out = '<span class="item_price_old">&nbsp;'.$item['price_old'].'&nbsp;</span>';
		$price_out = '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span class="item_price_discount" itemprop="price">&nbsp;'.$item['price'].'&nbsp;</span><span class="item_rub" itemprop="priceCurrency"> '.$shopSettings->currency.'</span></div>';
	}
	else
	{
		$discount_out = '';
		$price_out = '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span class="item_block_price" itemprop="price">'.$item['price'].'</span><span class="item_block_rub" itemprop="priceCurrency"> '.$shopSettings->currency.'</span></div>';
		$price_old_out = '';
	}

	// Новинка
	if($item['new'] == 1){$new_out = '<a class="item_sticker_new" href="/shop/item/'.$item['id'].'"></a>';}
	else{$new_out = '';}

	// Хит
	if($item['hit'] == 1){$hit_out = '<a class="item_sticker_hit" href="/shop/item/'.$item['id'].'"></a>';}
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
		$quantity_out = '<a class="item_sticker_order" href="/shop/item/'.$item['id'].'"></a>';
	}
	else
	{
		$quantity_out = '';
	}

	$item_char = '';
	$item_char_tr = '';
	$i = 0;

	if(isset($item['char']))
	{
		// перебираем массив по названиям характеристик
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

	$item_char .= '<table class="shop_char_table">';
	$item_char .= $item_char_tr;
	$item_char .= '</table>';


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
		<br/>
		<div class="item_photo_container">
			<div class="item_photo_container_border">'.$new_out.$discount_out.$hit_out.$quantity_out.$photo_big.'</div>
		</div>
		'.$rating_out.'
		'.$img_more_out.'
	';

	if($item['price'] != 0)
	{
		$out .= '<div class="item_price_out">'.$price_old_out.$price_out.'</div>';
	}

	$out .= '
		<div class="item_but"><input type="submit" value="'.$shopSettings->sticker_add_to_cart.'" class="item_button" name="shopbutton" /></div>
		'.$quantity_status.'
		'.$question_out.'
		<br/>
		<div>'.$item_char.'</div>
		<hr class="item_hr">
		<div class="item_intro_text">'.$item['intro_text'].'</div>
		<div itemprop="description" class="item_full_text">'.$item['full_text'].'</div>
		<input type="hidden" value="'.$item['id'].'" name="item_id" />

		<input id="item_id" type="hidden" value="'.$item['id'].'" name="item_id" />
		</div>
		'.$shop_related_items_out.'
		</form>
	';


	return $out;

}
?>
