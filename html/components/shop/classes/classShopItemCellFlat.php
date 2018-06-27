<?php
defined('AUTH') or die('Restricted access');

class classShopItemCellFlat extends classShopItem
{
	protected function templateItem($_item)
	{
		$id = $_item['id'];
		$title = $_item['title'];
		$price = $_item['price'];
		$price_old = $_item['price_old'];
		$discount = $_item['discount'];
		$new = $_item['new'];
		$photo_path = $_item['photo'];
		$photo_big_path = $_item['photo_big'];
		$photo_width = $this->shopSettings->x_small;
		$photo_height = $this->shopSettings->y_small;

		if($this->shopSettings->basket_type == 1){$click = 'onclick="shop_buy_fly('.$id.');"';}
		else{$click = 'onclick="shop_buy('.$id.');"';}

		if(!is_file($_SERVER['DOCUMENT_ROOT']."/components/shop/photo/".$photo_path))
		{
			$item_photo_small_out = '<div class="no-photo" style="width:'.$photo_width.'px;height:'.$photo_height.'px;"></div>';
		}
		else
		{
			$item_photo_small_out =
			'
			<img border="0" alt="'.$title.'" src="/components/shop/photo/'.$photo_path.'" id="shop_item_img_'.$id.'" />
			';
		}

		if($new == 1)
		{
			$new_out = '<div class="section_item_cell_flat_new" href="/shop/item/'.$id.'">Новинка</div>';
		}
		else
		{
			$new_out = '';
		}

		if($discount == 1)
		{
			$discount_out = '<div class="section_item_cell_flat_discount" href="/shop/item/'.$id.'">Скидка</div>';
			$price_old_out = '<span class="section_item_cell_flat_price_old">'.$price_old.'</span>';
			$item_price_out = '<span class="section_item_cell_flat_price_discount">'.$price.'<span class="section_item_cell_flat_rub_discount"> руб.</span></span>';
		}
		else
		{
			$discount_out = '';
			$price_old_out = '';
			$item_price_out = $price.'<span class="section_item_cell_flat_rub"> руб.</span>';
		}

		echo '
			<div class="section_item_cell_flat">
				<div class="section_item_cell_flat_float">
					'.$new_out.'
					'.$discount_out.'
				</div>
				<a href="/shop/item/'.$id.'" class="section_item_cell_flat_image" style="width:'.$photo_width.'px; height:'.$photo_height.'px;">'.$item_photo_small_out.'</a>
				<a href="/shop/item/'.$id.'" class="section_item_cell_flat_title" style="width:'.$photo_width.'px;">'.$title.'</a>
				<div class="section_item_cell_flat_desc">
					<div class="section_item_cell_flat_desc_price">'.$price_old_out.$item_price_out.'</div>
					<div class="section_item_cell_flat_desc_buy">
						<div class="section_item_cell_flat_desc_buy_button" '.$click.'>В корзину</div>
					</div>
				</div>
			</div>
		';
	}
};

?>
