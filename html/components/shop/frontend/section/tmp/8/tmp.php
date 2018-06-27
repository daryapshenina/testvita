<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/../../lang/'.LANG.'.php';
include_once($root.'/components/shop/classes/Chars.php');

class classShopSectionItem extends classShopItem
{
	protected function templateItem($_item)
	{
		global $root, $shopSettings, $domain, $frontend_edit, $char_arr;

		$size = array(
			'x' => $shopSettings->x_small,
			'y' => $shopSettings->y_small
		);

		$image = '';
		$magnifier = '';
		$price = '';
		$discount = '';
		$new = '';
		$hit = '';
		$order = '';
		$chars = '';

		/* image */
		if(is_file($root.'/components/shop/photo/'.$_item['photo']))
			$image = '<a href="/shop/item/'.$_item['id'].'" class="section_item_a"><img border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$_item['photo'].'" /></a>';
		else
			$image = '<div class="no-photo" style="width:'.$size['x'].'px;height:'.$size['y'].'px;"></div>';

		/* big image */
		if(is_file($root.'/components/shop/photo/'.$_item['photo_big']))
		{
			if(MobileDetector::getDevice() == NULL){$magnifier = '<a href="/components/shop/photo/'.$_item['photo_big'].'" class="section_item_magnifier show" title="'.LANG_SHOP_SECTION_FULLSCREEN.'"></a>';}
			else{$magnifier = '';}
		}

		/* price */
		if($_item['price'] > 0)
		{
			$_item['price'] = number_format($_item['price'], 0, '', ' ');
			$_item['price_old'] = number_format($_item['price_old'], 0, '', ' ');

			if($_item['discount'])
			{
				$price = '
					<span class="section_item_price_old">
						<span>'.$_item['price_old'].'</span>
						<span> '.$shopSettings->currency.'</span>
					</span>
				';

				$discount = '<a class="section_item_discount" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_sale.'</a>';
			}

			$price .= '
				<span class="section_item_price">
					<span>'.$_item['price'].'</span>
					<span> '.$shopSettings->currency.'</span>
				</span>
			';
		}

		// Новинка
		if($_item['new']) $new = '<a class="section_item_new" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_new.'</a>';

		// Хит
		if($_item['hit']) $hit = '<a class="section_item_hit" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_hit.'</a>';

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

		// Под заказ
		if($_item['quantity'] == '0' && $this->shopSettings->item_quantity != 0 && $this->shopSettings->grouping != '1'){$order = '<a class="section_item_order" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_order.'</a>';}
		else{$order = '';}


		/* char */
		$Chars = new Chars($_item['id'], $_item['group_identifier']);
		$charsArraySource = $Chars->getArray();
		$charsArray = array();
		$counter = 0;

		foreach($charsArraySource as $iter)
		{
			$name = $iter['name'];
			$charsArray[$name]['unit'] = $iter['unit'];
			$charsArray[$name]['value'][] = $iter['value'];


		}

		foreach($charsArray as $name => $iter)
		{
			$iter['value'] = array_unique($iter['value']);
			asort($iter['value']);

			$values = '';

			foreach($iter['value'] as $value)
				$values .= $value.', ';

			$values = rtrim($values, ', ');

			$chars .= '
				<tr>
					<td>
						'.$name.':
					</td>
					<td>
						'.$values.'
						'.$iter['unit'].'
					</td>
				</tr>
			';

			$counter++;

			if($counter >= 5)
				break;
		}

		if(count($charsArray) > 0)
		{
			$chars = '
				<div class="section_item_chars">
					<table>
						<tbody>
							'.$chars.'
						</tbody>
					</table>
				</div>
			';
		}


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

		$out = '
			<div '.$data_type.' class="'.$edit_mode_class.'section_item">
				<div>
					<div style="width:'.$size['x'].'px;">
						<div style="width:'.$size['x'].'px; height:'.$size['y'].'px;">
							<div class="section_item_float">
								'.$new.'
								'.$discount.'
								'.$hit.'
								'.$order.'
							</div>
							'.$image.'
							'.$magnifier.'
						</div>
						'.$rating_out.'
						<div>'.$price.'</div>
					</div>
					<div>
						<div><a href="/shop/item/'.$_item['id'].'">'.$_item['title'].'</a></div>
						<div>'.$chars.'</div>
						<div>'.$_item['intro_text'].'</div>
					</div>
				</div>
			</div>';

		return $out;
	}
};

?>
