<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/../../lang/'.LANG.'.php';
include_once $root."/classes/MobileDetector.php";

$time_encode = Auth::encode(time());

$head->addFile('/js/vanilla-masker.min.js');
$head->addCode('
<script type="text/javascript">
function item_section_order(_id, _title)
{
	var out = \'<div class="section_item_form_container"><h2>Заказать "\' + _title + \'"</h2><div class="section_item_form_text">Заполните все поля и наш менеджер свяжется с Вами в ближайшее время.</div><form class="section_item_form" method="post" action="/shop/landing_order">\';
	out += \'<div><input class="input" type="text" name="name" required  placeholder="Ваше имя"><input id="section_item_form_phone" class="input" type="text" name="phone" required placeholder="Номер телефона">\';
	out += \'<input class="section_item_lastname" name="lastname" type="text" value=""><input name="item_id" type="hidden" value="\' + _id + \'"><input name="item_title" type="hidden" value="\' + _title + \'">\';
	out += \'<input type="hidden" name="dt" value="'.$time_encode.'"><input class="input section_item_submit" type="submit" value="Заказать сейчас!"></div></form></div>\';
	DAN.modal.add(out, 350);
	var phone = document.getElementById("section_item_form_phone");
	VMasker(phone).maskPattern("9 (999) 999-99-99");
}
</script>
');


class classShopSectionItem extends classShopItem
{
	protected function templateItem($_item)
	{
		global $root, $domain, $frontend_edit, $shopSettings;

		$photo_width = $shopSettings->x_small;
		$photo_height = $shopSettings->y_small;

		$img_more_out = '';

		// Фото
		if($_item['photo'] != '' && is_file($root."/components/shop/photo/".$_item['photo_big']))
		{
			$item_photo_big_out ='<img border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$_item['photo_big'].'" id="shop_item_img_'.$_item['id'].'" />';

			if($_item['photo_more'] != '')
			{
				$img_arr = explode(';', $_item['photo_more']);

				$n = count($img_arr) - 1;
				for($i = 0; $i < $n; $i++)
				{
					$img_arr_big[$i] = str_replace('.jpg', '_.jpg', $img_arr[$i]);
					if(MobileDetector::getDevice() == NULL){$show_class = 'show2 '; $modal = '';}else{$show_class = ''; $modal = 'onclick="dan_modal_2(\'<img style=&quot;width:100%;&quot; src=&quot;/components/shop/photo/'.$img_arr_big[$i].'&quot;>\')"';}
					$img_more_out .= '<img '.$modal.' class="'.$show_class.'section_item_img_more" border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$img_arr[$i].'" longdesc="/components/shop/photo/'.$img_arr_big[$i].'"/>';
				}
				$img_more_out = '<div class="section_item_img_more_out">'.$img_more_out.'</div>';
			}
		}
		else
		{
			$item_photo_big_out = '<div class="no-photo" id="shop_item_img_'.$_item['id'].'" ></div>';
		}

		// Новинки
		if($_item['new'] == 1)
		{
			$new_out = '<a class="section_item_new" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_new.'</a>';
		}
		else
		{
			$new_out = '';
		}

		// Хиты
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

		// Скидка
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

		$item_chars_arr = $shopItemChars->getArray();

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

		// Перебираем массив по названиям характеристик
		foreach ($chars_arr as $n => $char_arr)
		{
			if(count($char_arr['value']) > 0)
			{
				$value_out = '';
				$i = 1;

				// Перебираем характеристику
				foreach ($char_arr['value'] as $value)
				{
					// Ставим запятую между значениями характеристик и пропускаем в конце
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

			if($char_arr['unit'] == ''){$unit_out = '';} else {$unit_out = ' '.$char_arr['unit'];}
			if($value_out != '')
			{
				$item_char .= '<div class="section_item_char_0"><div class="section_item_char_1">'.$n.':</div><div>&nbsp;</div><div class="section_item_char_2">'.$value_out.$unit_out.'</div></div>';
			}
		}

		$item_char_out .= '<div class="section_item_char">';
		$item_char_out .= $item_char;
		$item_char_out .= '</div>';

		$item_intro_out = '';
		if($_item['intro_text'] != ''){$item_intro_out = '<div class="section_item_intro_text">'.$_item['intro_text'].'</div>';}

		// frontend редактирование
		if($frontend_edit == 1 && $this->getTypeOut() == '0')
		{
			$class_edit_mode = 'edit_mode ';
			$data_tipe = 'data-type="com_shop_item" data-id="'.$_item['id'].'"';
		}
		else
		{
			$class_edit_mode = '';
			$data_tipe = '';
		}

		$item_inner = '
			<div '.$data_tipe.' class="'.$class_edit_mode.'section_item flex_row">
				<div class="section_item_image">
					<div class="section_item_float">
						'.$new_out.'
						'.$discount_out.'
						'.$hit_out.'
						'.$custom_out.'
					</div>
					<div><div>'.$item_photo_big_out.'</div>'.$img_more_out.'</div>
				</div>
				<div class="section_item_text">
					<span class="section_item_title" >'.$_item['title'].'</span>
					'.$item_char_out.$rating_out.'
					<div class="section_item_desc">
						'.$_item['intro_text'].'
						<div class="section_item_price">'.$price_old_out.$price_out.'</div>
						<div class="section_item_order">
							<div class="section_item_order_button" onclick="item_section_order(\''.$_item['id'].'\', \''.$_item['title'].'\')">Заказать</div>
						</div>
					</div>
				</div>
			</div>
		';

		return $item_inner;
	}
};

?>
