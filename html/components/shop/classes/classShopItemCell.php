<?php
defined('AUTH') or die('Restricted access');

class classShopItemCell extends classShopItem
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

		if($photo_big_path != '')
		{
			$item_photo_big_out = '<a href="/components/shop/photo/'.$photo_big_path.'" class="shop_item_lupa show" title="Во весь экран"></a>';
		}
		else
		{
			$item_photo_big_out = '';
		}

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

		if($discount == 1)
		{
			$discount_out = '<a class="section_item_cell_discount" href="/shop/item/'.$id.'"></a>';
			$price_old_out = '<span class="section_item_cell_price_old">&nbsp;'.$price_old.'&nbsp;</span>';
			$item_price_out = '<span class="section_item_cell_price_discount">'.$price.'<span class="section_item_cell_rub_d">р</span></span>';
		}
		else
		{
			$discount_out = '';
			$item_price_out = $price.'<span class="section_item_cell_rub">р</span>';
			$price_old_out = '';
		}

		if($new == 1)
		{
			$new_out = '<a class="section_item_cell_new" href="/shop/item/'.$id.'"></a>';
		}
		else
		{
			$new_out = '';
		}

		if($price != 0)
		{
			$item_price_out = '<span class="section_item_cell_but_price">'.$price_old_out.$item_price_out.'</span>';
		}
		else
		{
			$item_price_out = '<span class="section_item_cell_but_price"></span>';
		}

		echo '
		<div class="section_item_cell">
			'.$new_out.$discount_out.'
			<div class="section_item_cell_title" align="center" style="width:'.$photo_width.'px;"><a href="/shop/item/'.$id.'"  title="Подробнее">'.$title.'</a></div>
			<div style="width:'.$photo_width.'px; height:'.$photo_height.'px; position:relative;">
				'.$item_photo_big_out.'
				<a href="/shop/item/'.$id.'">'.$item_photo_small_out.'</a>
			</div>
			<br/>
			<table border="0" width="100%" cellpadding="0" style="border-collapse: collapse" class="section_item_cell_but" align="center" '.$click.' title="Добавить в корзину">
				<tr>
					<td>
						<div class="section_item_cell_but_left">'.$item_price_out.'</div>
					</td>
					<td width="40">
						<div class="section_item_cell_but_right">
							<img alt="'.$title.'" src="/components/shop/frontend/tmp/images/basket.png" class="section_item_cell_cart_img"/>
						</div>
					</td>
				</tr>
			</table>

		</div>
		';
	}
};

?>
