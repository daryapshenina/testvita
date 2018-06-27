<?php
// DAN 2015
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/../../lang/'.LANG.'.php';

class classShopSectionItem extends classShopItem
{
	protected function templateItem($_item)
	{
		global $root, $shopSettings, $domain, $frontend_edit;

		if($_item['photo'] != '' && is_file($root."/components/shop/photo/".$_item['photo']))
		{
			$item_photo_small_out ='<img border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$_item['photo'].'" id="shop_item_img_'.$_item['id'].'" />';
		}
		else
		{
			$item_photo_small_out = '<div class="no-photo" style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;" id="shop_item_img_'.$_item['id'].'"></div>';
		}

		if($_item['photo_big'] != '')
		{
			if(MobileDetector::getDevice() == NULL){$item_photo_big_out = '<a href="/components/shop/photo/'.$_item['photo_big'].'" class="section_item_lupa show" title="'.LANG_SHOP_SECTION_FULLSCREEN.'"></a>';}
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
				$price_out = '<span>'.$_item['price'].'<span class="section_item_rub"> '.$shopSettings->currency.'</span></span>';
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
				$price_out = $_item['price'].'<span class="section_item_rub"> '.$shopSettings->currency.'</span>';
			}
			else {$price_out = '';}
		}

		// Под заказ
		if($_item['quantity'] == '0' && $this->shopSettings->item_quantity != 0 && $this->shopSettings->grouping != '1'){$custom_out = '<a class="section_item_order" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_order.'</a>';}
		else{$custom_out = '';}

		$group_identifier = $_item['group_identifier'];

		include_once($root.'/components/shop/classes/Chars.php');
		$shopItemChars = new Chars($_item['id'], $group_identifier);

		$item_chars_arr = $shopItemChars->getArray();

		$chars_arr = array();

		// Перебираем характеристикии заносим одинаковые в массив
		foreach($item_chars_arr as $ch)
		{
			$name = $ch['name'];
			$chars_arr[$name]['unit'] = $ch['unit'];
			$chars_arr[$name]['value'][] = $ch['value'];
		}

		$razmer_out = '';
		$cvet_out = '';
		$char_other_arr = array(); // массив прочих характеристик - выводим в случае если размер и цвет незаполнены
		$c = 1; // считает характеристики
		// перебираем массив по названиям характеристик
		foreach ($chars_arr as $char_name => $char_arr)
		{
			if(count($char_arr['value']) > 0)
			{
				$char_arr['value'] = array_unique($char_arr['value']); // удаляем повторяющиеся значения
				asort($char_arr['value']); // сортируем массив - значения от меньшего - к большему по алфавиту

				if(mb_strtolower($char_name, 'UTF-8') == 'размер')
				{
					$i = 1;
					// перебираем характеристику
					foreach ($char_arr['value'] as $value)
					{
						// ставим запятую между значениями характеристик и пропускаем в конце
						if(count($char_arr['value']) > $i)
						{
							if($value != ''){$razmer_out .= $value.", ";}
						}
						else
						{
							$razmer_out .= $value;
						}

						$i++;
					}

					$c++;
				}
				elseif(mb_strtolower($char_name, 'UTF-8') == 'цвет')
				{
					$i = 1;
					// перебираем характеристику
					foreach ($char_arr['value'] as $value)
					{
						// ставим запятую между значениями характеристик и пропускаем в конце
						if(count($char_arr['value']) > $i)
						{
							if($value != ''){$cvet_out .= $value.", ";}
						}
						else
						{
							$cvet_out .= $value;
						}

						$i++;
					}
					$c++;
				}
				else // прочие характеристики, не размер и не цвет
				{
					$i = 1;
					// перебираем характеристику
					foreach ($char_arr['value'] as $value)
					{
						// ставим запятую между значениями характеристик и пропускаем в конце
						if(count($char_arr['value']) > $i)
						{
							if($value != ''){@$char_other_arr[$char_name] .= $value.", ";}
						}
						else
						{
							@$char_other_arr[$char_name] .= $value;
						}

						$i++;
					}
					$c++;
				}

			}

			if ($c > 4){break;} // ограничение числа выводимых характеристик = 2 (цвет и размер + 2 им на подмену, ели они отсутствуют) и прерываем цикл
		}

		if($razmer_out != ''){$razmer_out = '<b>Размер:</b> '.$razmer_out.'<br>';}
		if($cvet_out != ''){$cvet_out = '<b>Цвет:</b> '.$cvet_out.'';}

		$char_other_out = '';
		if($razmer_out == '' && $cvet_out == '') // Если нет ни цвета, ни размера - выводим 2 другие характеристики
		{
			foreach($char_other_arr as $char_other_name => $char_other_value)
			{
				$char_other_out .= '<b>'.$char_other_name.':</b> '.$char_other_value.'<br>';
			}
		}

		$item_char = '<span style="margin-right:20px;">'.$razmer_out.$cvet_out.$char_other_out.'</span>';


		$item_char_out = '<div class="section_item_char" style="width:'.$shopSettings->x_small.'px;">';
		$item_char_out .= $rating_out.$item_char;
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
			<div '.$data_type.' class="'.$edit_mode_class.'section_item" style="width:'.$shopSettings->x_small.'px;">
				<div>
					<div class="section_item_float">
						'.$new_out.'
						'.$discount_out.'
						'.$hit_out.'
						'.$custom_out.'
					</div>
					<div class="section_item_image" style="width:'.$shopSettings->x_small.'px; height:'.$shopSettings->y_small.'px;">
						<a class="section_item_a" href="/shop/item/'.$_item['id'].'">'.$item_photo_small_out.'</a>
						'.$item_photo_big_out.'
					</div>
					<div class="section_item_desc">
						<a href="/shop/item/'.$_item['id'].'" class="section_item_title" style="width:'.$shopSettings->x_small.'px;">'.$_item['title'].'</a>
						<div class="section_item_price">'.$price_old_out.$price_out.'</div>
					</div>
					'.$item_char_out.'
				</div>
			</div>
		';

		return $item_inner;
	}
};

?>
