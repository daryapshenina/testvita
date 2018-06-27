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
			$item_photo_small_out ='<img border="0" alt="'.$_item['title'].'" src="/components/shop/frontend/tmp/images/no-photo.png" id="shop_item_img_'.$_item['id'].'" />';
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
		if($_item['quantity'] == '0' && $shopSettings->item_quantity != 0 && $shopSettings->grouping != '1'){$custom_out = '<a class="section_item_order" href="/shop/item/'.$_item['id'].'">'.$shopSettings->sticker_order.'</a>';}
		else{$custom_out = '';}

		$group_identifier = $_item['group_identifier'];

		/**/

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

		/**/

		if($shopSettings->basket_type == 1) // Летающая корзина
			$form_submit_out = 'onclick="shop_buy_fly('.$_item['id'].');"';
		else
			$form_submit_out = 'onclick="shop_buy('.$_item['id'].');"';

		/**/

		$item_inner = '
			<div '.$data_type.' class="'.$edit_mode_class.'section_item" style="width:'.($shopSettings->x_small + 20).'px;">
				<div class="section_item_border">
					<div class="section_item_image">
						<div class="section_item_float">
							'.$new_out.'
							'.$discount_out.'
							'.$hit_out.'
							'.$custom_out.'
						</div>
						<a class="section_item_a" href="/shop/item/'.$_item['id'].'">'.$item_photo_small_out.'</a>
						'.$item_photo_big_out.'
					</div>
					<div class="section_item_desc">
						<a href="/shop/item/'.$_item['id'].'" class="section_item_title">'.$_item['title'].'</a>
						'.$rating_out.'
						<div class="section_item_price">'.$price_old_out.$price_out.'</div>
					</div>
					<div class="section_item_more">
						<div class="section_item_more_inside">
							<div>
								<div class="shop_quantity"><div id="quantity_minus" onclick="item_quantity('.$_item['id'].',-1);">-</div><input type="text" id="shop_item_num_'.$_item['id'].'" class="input_quantity" name="input_quantity" value="1"><div id="quantity_plus" onclick="item_quantity('.$_item['id'].',1);">+</div></div>
							</div>
							<div>
								<input '.$form_submit_out.' type="submit" value="'.$shopSettings->sticker_add_to_cart.'" class="but_cart" name="shopbutton" />
							</div>
						</div>
					</div>
				</div>
			</div>
		';

		return $item_inner;
	}
};

?>
