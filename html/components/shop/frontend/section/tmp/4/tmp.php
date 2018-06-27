<?php
// Плиткой - старый стиль
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/../../lang/'.LANG.'.php';

class classShopSectionItem extends classShopItem
{
	protected function templateItem($_item)
	{
		global $root, $shopSettings, $domain, $frontend_edit;

		if($shopSettings->basket_type == 1){$click = 'onclick="shop_buy_fly('.$_item['id'].');"';}
		else{$click = 'onclick="shop_buy('.$_item['id'].');"';}

		// Изображение
		if($_item['photo'] != '')
		{
			$photo_out = '<img border="0" alt="'.$_item['title'].'" src="/components/shop/photo/'.$_item['photo'].'" id="shop_item_img_'.$_item['id'].'" >';
		}
		else{$photo_out = '<div class="no-photo" style="width:'.$shopSettings->x_small.'px;height:'.$shopSettings->y_small.'px;" id="shop_item_img_'.$_item['id'].'" ></div>';}

		if($_item['photo_big'] != '')
		{
			if(MobileDetector::getDevice() == NULL)
			{
				$photo_big_out = '<a href="/components/shop/photo/'.$_item['photo_big'].'" class="shop_item_lupa show" title="'.LANG_SHOP_SECTION_FULLSCREEN.'"></a>';
			}
			else{$photo_big_out = '';}
		}
		else{$photo_big_out = '';}

		// Скидка
		if($_item['discount'] == 1)
		{
			if($_item['price'] > 0)
			{
				$_item['price'] = number_format($_item['price'], 0, '', ' ');
				$_item['price_old'] = number_format($_item['price_old'], 0, '', ' ');			
				$discount_out = '<a class="section_item_discount" href="/shop/item/'.$_item['id'].'"></a>';
				$price_old_out = '<span class="section_item_price_old">'.$_item['price_old'].'</span>';
				$price_out = '<span class="section_item_price_discount">'.$_item['price'].'<span class="section_item_rub_discount"> '.$shopSettings->currency.'</span></span>';
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

		// Новинка
		if($_item['new'] == 1)
		{
			$new_out = '<a class="section_item_new" href="/shop/item/'.$_item['id'].'"></a>';
		}
		else{$new_out = '';}
		
		// Хит
		if($_item['hit'] == 1)
		{
			$hit_out = '<a class="section_item_hit" href="/shop/item/'.$_item['id'].'"></a>';
		}
		else{$hit_out = '';}		

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
		if($_item['quantity'] == '0' && $shopSettings->item_quantity != 0 && $shopSettings->grouping != '1'){$custom_out = '<a class="section_item_order" href="/shop/item/'.$_item['id'].'"></a>';}
		else{$custom_out = '';}
		
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
			'.$new_out.$discount_out.$custom_out.$hit_out.'
			<div class="section_item_title" align="center" style="width:'.($shopSettings->x_small + 8).'px;"><a href="/shop/item/'.$_item['id'].'">'.$_item['title'].'</a></div>
			<div style="width:'.($shopSettings->x_small + 8).'px; height:'.($shopSettings->y_small + 8).'px; position:relative;">
				'.$photo_big_out.'
				<a href="/shop/item/'.$_item['id'].'">'.$photo_out.'</a>
			</div>
			'.$rating_out.'
			<div class="section_item_price">'.$price_out.'</div>
			<div class="shop-but" ><input type="submit" '.$click.' value="'.$shopSettings->sticker_add_to_cart.'" class="shop-button" name="shopbutton" /></div>
		</div>
		';

		return $item_inner;
	}
};

?>
