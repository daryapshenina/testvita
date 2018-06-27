<?php
// DAN 2014
// выводит содержимое товара в разделе.
defined('AUTH') or die('Restricted access');

if($shopSettings->getValue('basket_type') == 1){$click = 'onclick="shop_buy_fly('.$item_id.');"';}
else{$click = 'onclick="shop_buy('.$item_id.');"';}

if($item_photo_big != '')
{
	$item_photo_big_out = '<a href="http://'.$site.'/components/shop/photo/'.$item_photo_big.'" class="section_item_cell_flat_lupa show" title="Во весь экран"></a>';
}
else
{
	$item_photo_big_out = '';
}

// Новинка
if($item_new == 1)
{
	$new_out = '<div class="section_item_cell_flat_new" href="http://'.$site.'/shop/item/'.$item_id.'">Новинка</div>';
}
else
{
	$new_out = '';
}

// Скидка
if($item_discount == 1)
{
	$discount_out = '<div class="section_item_cell_flat_discount" href="http://'.$site.'/shop/item/'.$item_id.'">Скидка</div>';
	if ($item_price > 0)
	{
		$price_old_out = '<span class="section_item_cell_flat_price_old">'.$item_price_old.'</span>';
		$item_price_out = '<span class="section_item_cell_flat_price_discount">'.$item_price.'<span class="section_item_cell_flat_rub_discount"> руб.</span></span>';
	}
	else
	{
		$price_old_out = '';
		$item_price_out = '';
	}
}
else
{
	$discount_out = '';
	$price_old_out = '';
	if ($item_price > 0)
	{
		$item_price_out = $item_price.'<span class="section_item_cell_flat_rub"> руб.</span>';
	}
	else
	{
		$item_price_out = '';
	}

}

// Под заказ
if($item_quantity <= 0 && $shopSettings->getValue('item_quantity') != 0){$custom_out = '<div class="section_item_cell_flat_order" href="http://'.$site.'/shop/item/'.$item_id.'">Под заказ</div>';}
else{$custom_out = '';}


$item_inner = '
	<div class="section_item_cell_flat_float">
		'.$new_out.'
		'.$discount_out.'
		'.$custom_out.'
	</div>
	<div class="section_item_cell_flat_image" style="width:'.($shopSettings->getValue('x_small')).'px; height:'.($shopSettings->getValue('y_small')).'px;">
		<a href="http://'.$site.'/shop/item/'.$item_id.'">'.$item_photo_small_out.'</a>
		'.$item_photo_big_out.'
	</div>
	<div><a href="http://'.$site.'/shop/item/'.$item_id.'" class="section_item_cell_flat_title" style="width:'.($shopSettings->getValue('x_small')).'px;">'.$item_title.'</a></div>
	'.$item_char_out.'
	<div class="section_item_cell_flat_desc" style="width:'.($shopSettings->getValue('x_small')).'px;">
		<div class="section_item_cell_flat_desc_price">'.$price_old_out.$item_price_out.'</div>
		<div class="section_item_cell_flat_desc_buy">
			<div class="section_item_cell_flat_desc_buy_button" '.$click.'>В корзину</div>
		</div>
	</div>
';

// frontend редактирование
if($frontend_edit == 1){$item_inner = '<div class="edit_mode" data-type="com_shop_item" data-id="'.$item_id.'">'.$item_inner.'</div>';}

$items_out .= '<div class="section_item_cell_flat">'.$item_inner.'</div>';

?>
