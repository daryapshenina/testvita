<?php
// Плоской плиткой - 2
// Ищем характеристику с названием "цвет", "цвета", "color" - по точному вхождению, без учёта регистра
// Если такая характеристика есть выводим цвета из папки /files/shop/chars/colors/color.jpg, где color - значение цвета; если цвет написан по-русски - ищем транслит -> "белый" >>> "belyj.jpg"

defined('AUTH') or die('Restricted access');

include_once __DIR__.'/../../lang/'.LANG.'.php';

class classShopSectionItem extends classShopItem
{
	protected function templateItem($_item)
	{
		global $root, $domain, $shopSettings, $frontend_edit;

		if($shopSettings->basket_type == 1){$click = 'onclick="shop_buy_fly('.$_item['id'].');"';}
		else{$click = 'onclick="shop_buy('.$_item['id'].');"';}

		if($_item['photo'] != '' && is_file($root."/components/shop/photo/".$_item['photo']))
		{
			$item_photo_small_out ='<img border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$_item['photo'].'" id="shop_item_img_'.$_item['id'].'" />';
		}
		else
		{
			$item_photo_small_out = '<div class="no-photo" style="width:'.$shopSettings->x_small.'px;" id="shop_item_img_'.$_item['id'].'" ></div>';
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


		$color_arr = array("цвет", "цвета", "color");
		$item_color = '';
		$item_color_out = '';

		// перебираем массив по названиям характеристик
		foreach ($chars_arr as $n => $char_arr)
		{
			if(count($char_arr['value']) > 0)
			{
				if(in_array(mb_strtolower($n), $color_arr))
				{
					// перебираем характеристику - цвет
					foreach ($char_arr['value'] as $value)
					{
						if($value != '')
						{
							// Т.к. цвета идут через ";" "белый;красный;синий", то разбиваем эту строчку на массив через ";"

							$color_arr = explode(';', $value);

							foreach ($color_arr as $color)
							{
								$color_name = checkingeditor($color);
								if(file_exists($root.'/files/shop/chars/colors/'.$color_name.'.jpg'))
								{
									$item_color .= '<img class="section_item_color_img" src="/files/shop/chars/colors/'.$color_name.'.jpg" alt="'.$color.'">';
								}

							}
						}
					}

					$item_color_out = '<div class="section_item_color">'.$item_color.'</div>';
					break;
				}
			}
		}

		if($_item['intro_text'] != '')
		{

			if(mb_strlen($_item['intro_text']) > 30){$_item['intro_text'] = mb_substr(strip_tags(preg_replace('/&(.+?);/',' ',$_item['intro_text'])), 0, 30).'...';}
			$intro_text_out = '<div class="section_item_intro_text" style="width:'.$shopSettings->x_small.'px;">'.$_item['intro_text'].'</div>';
		}
		else {$intro_text_out = '';}

		// frontend редактирование
		if($frontend_edit == 1 && $this->getTypeOut() == '0')
		{
			$edit_mode_class = 'edit_mode ';
			$data_type = ' data-type="com_shop_item" data-id="'.$_item['id'].'" ';
		}
		else
		{
			$edit_mode_class = '';
			$data_type = '';
		}

		// <div class="section_item_buy_button" style="top:'.($shopSettings->x_small/2).'px;" '.$click.'>'.$shopSettings->sticker_add_to_cart.'</div>

		$item_inner = '
			<div class="'.$edit_mode_class.'section_item" '.$data_type.'>
				<div class="section_item_float">
					'.$new_out.'
					'.$discount_out.'
					'.$hit_out.'
					'.$custom_out.'
				</div>
				<div class="section_item_image" style="width:'.$shopSettings->x_small.'px;">
					<a href="/shop/item/'.$_item['id'].'">'.$item_photo_small_out.'</a>
				</div>
				'.$item_color_out.'
				'.$intro_text_out.'
				<a href="/shop/item/'.$_item['id'].'" class="section_item_title" style="width:'.$shopSettings->x_small.'px;">'.$_item['title'].'</a>
				'.$rating_out.'
				<div class="section_item_price">'.$price_old_out.$price_out.'</div>
				<div class="section_item_buy_button" '.$click.'>'.$shopSettings->sticker_add_to_cart.'</div>
			</div>
		';

		return $item_inner;
	}
};

?>