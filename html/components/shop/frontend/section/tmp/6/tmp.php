<?php
// DAN 2015
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/../../lang/'.LANG.'.php';

class classShopSectionItem extends classShopItem
{
	protected function templateItem($_item)
	{
		global $root, $shopSettings, $domain, $frontend_edit;

		$photo_width = $shopSettings->x_small;
		$photo_height = $shopSettings->y_small;

		if($this->shopSettings->basket_type == 1){$click = 'onclick="shop_buy_fly('.$_item['id'].');"';}
		else{$click = 'onclick="shop_buy('.$_item['id'].');"';}

		if($_item['photo'] != '' && is_file($root."/components/shop/photo/".$_item['photo']))
		{
			$item_photo_small_out ='<img border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$_item['photo'].'" id="shop_item_img_'.$_item['id'].'" />';
		}
		else
		{
			$item_photo_small_out = '<div class="no-photo" style="width:'.$photo_width.'px;height:'.$photo_height.'px;" id="shop_item_img_'.$_item['id'].'" ></div>';
		}

		if($_item['photo_big'] != '')
		{
			if(MobileDetector::getDevice() == NULL)
			{
				$item_photo_big_out = '<a href="/components/shop/photo/'.$_item['photo_big'].'" class="section_item_lupa show" title="'.LANG_SHOP_SECTION_FULLSCREEN.'"></a>';
			}
			else{$item_photo_big_out = '';}
		}
		else
		{
			$item_photo_big_out = '';
		}

		if($_item['new'] == 1)
		{
			$new_out = '<a class="section_item_new" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_new.'</a>';
		}
		else
		{
			$new_out = '';
		}

		if($_item['hit'] == 1)
		{
			$hit_out = '<a class="section_item_hit" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_hit.'</a>';
		}
		else
		{
			$hit_out = '';
		}

		// Рейтинг
		if($_item['rating'] > 0)
		{
			$rating = round($_item['rating']);
			if($rating == 1) $rating_out = '<div class="section_item_rating">★☆☆☆☆</div>';
			if($rating == 2) $rating_out = '<div class="section_item_rating">★★☆☆☆</div>';
			if($rating == 3) $rating_out = '<div class="section_item_rating">★★★☆☆</div>';
			if($rating == 4) $rating_out = '<div class="section_item_rating">★★★★☆</div>';
			if($rating == 5) $rating_out = '<div class="section_item_rating">★★★★★</div>';
		}
		else{$rating_out = '';}

		if($_item['discount'] == 1)
		{
			if($_item['price'] > 0)
			{
				$_item['price'] = number_format($_item['price'], 0, '', ' ');
				$_item['price_old'] = number_format($_item['price_old'], 0, '', ' ');
				$discount_out = '<a class="section_item_discount" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_sale.'</a>';
				$price_old_out = '<span class="section_item_price_old">'.$_item['price_old'].'</span>';
				$price_out = '<span class="section_item_price_discount"><span>'.$_item['price'].'</span><span class="section_item_rub_discount"> '.$shopSettings->currency.'</span></span>';
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

			if($_item['price'] > 0)
			{
				$_item['price'] = number_format($_item['price'], 0, '', ' ');
				$_item['price_old'] = number_format($_item['price_old'], 0, '', ' ');
				$price_out = '<span>'.$_item['price'].'</span><span class="section_item_rub"> '.$shopSettings->currency.'</span>';
			}
			else {$price_out = '';}
		}

		// Под заказ
		if($_item['quantity'] == '0' && $shopSettings->item_quantity != 0 && $shopSettings->grouping != '1'){$custom_out = '<a class="section_item_order" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_order.'</a>';}
		else{$custom_out = '';}

		include_once($root.'/components/shop/classes/Chars.php');
		$shopItemChars = new Chars($_item['id']);

		$item_chars_arr = $shopItemChars->getArray($_item['id']);

		$chars_arr = array();

		// Перебираем характеристикии заносим одинаковые в массив
		foreach($item_chars_arr as $ch)
		{
			$name = $ch['name'];
			$chars_arr[$name]['unit'] = $ch['unit'];
			$chars_arr[$name]['value'][] = $ch['value'];
		}



		$item_char = '';
		$item_char_out = '';
		$c = 1; // считает характеристики
		// перебираем массив по названиям характеристик
		foreach ($chars_arr as $n => $char_arr)
		{
			if(count($char_arr['value']) > 0)
			{
				$value_out = '';
				$i = 1;

				// перебираем характеристику
				foreach ($char_arr['value'] as $value)
				{
					// ставим запятую между значениями характеристик и пропускаем в конце
					if(count($char_arr['value']) > $i)
					{
						if($value != ''){$value_out .= $value.", ";}
					}
					else
					{
						$value_out .= $value;
					}

					$i++;
				}
			}
			else
			{
				$value_out = '';
			}




			if ($c < 4) // ограничение числа выводимых характеристик
			{
				if($char_arr['unit'] == ''){$unit_out = '';} else {$unit_out = ' '.$char_arr['unit'];}
				if($value_out != '')
				{
					$item_char .= '<span style="margin-right:20px;">'.$value_out.$unit_out.'</span>';
					$c++;
				}
			}
			else {break;}


		}

		$item_char_out .= '<div class="section_item_char" style="width:'.$shopSettings->x_small.'px">';
		$item_char_out .= $item_char;
		$item_char_out .= '</div>';


		// frontend редактирование
		if($frontend_edit == 1 && $this->getTypeOut() == '0')
		{
			$edit_mode_class = 'edit_mode ';
			$data_type = 'data-type="com_shop_item" data-id="'.$_item['id'].'"';
		}
		else
		{
			$edit_mode_class = '';
			$data_type = '';
		}

		$item_inner = '
			<div '.$data_type.' class="'.$edit_mode_class.'section_item">
				<div class="section_item_float">
					'.$new_out.'
					'.$discount_out.'
					'.$hit_out.'
					'.$custom_out.'
				</div>
				<div class="section_item_image" style="width:'.$photo_width.'px; height:'.$photo_height.'px;">
					<a href="/shop/item/'.$_item['id'].'">'.$item_photo_small_out.'</a>
					'.$item_photo_big_out.'
				</div>
				<a href="/shop/item/'.$_item['id'].'" class="section_item_title" style="width:'.$photo_width.'px;">'.$_item['title'].'</a>
				'.$item_char_out.$rating_out.'
				<div class="section_item_desc">
					<div class="section_item_price">'.$price_old_out.$price_out.'</div>
					<div class="section_item_buy">
						<div class="section_item_buy_button" '.$click.'>'.$shopSettings->sticker_add_to_cart.'</div>
					</div>
				</div>
			</div>
		';

		return $item_inner;
	}
};

?>
