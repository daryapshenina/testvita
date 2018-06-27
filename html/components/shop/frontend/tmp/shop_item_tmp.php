<?php
// DAN обновление - январь 2014
// выводит товар
defined('AUTH') or die('Restricted access');

// Скидка
if($item_discount == 1)
{
	$discount_out = '<a class="shop_item_r_discount" href="/shop/item/'.$item_id.'"></a>';
	$price_old_out = '<div class="shop_item_price_old">&nbsp;'.$item_price_old.'&nbsp;</div>';
	$price_out = '<div style="text-align:center;"><span class="shop_item_price_discount">&nbsp;'.$item_price.'<span class="shop_item_rub">'.$shopSettings->getValue('currency').'</span>&nbsp;</span></div>';
}
else
{
	$discount_out = '';
	$price_out = '<div class="section_item_block_price">'.$item_price.'<span class="section_item_block_rub">'.$shopSettings->getValue('currency').'</span></div>';
	$price_old_out = '';
}


// Новинка
if($item_new == 1)
{
	$new_out = '<a class="shop_item_r_new" href="/shop/item/'.$item_id.'"></a>';
}
else
{
	$new_out = '';
}


if($shopSettings->getValue('view_item_card') == 1) // расширенный вид 1
{
	$out = '
		<form method="POST" action="/shop/basket">
			<div class="shop_item_extended_main">
				<div class="shop_item_extended_table">
					<div class="shop_item_extended_tr">
						<div class="shop_item_extended_td">
							<div class="shop_item_extended_photo">
								'.$new_out.$discount_out.$item_photobig.'
							</div>
						</div>
						<div class="shop_item_extended_td">
							<div class="shop_item_extended_intro">
								<h1 class="shop-item-title-3">'.$item_title.'</h1>
								'.$item_char.'
								<div class="shop_item_extended_introtext">'.$item_introtext.'</div>
								'.$price_old_out.$price_out.'
								<div class="shop-but" ><input type="submit" value="В корзину" class="shop_item_r_but" name="shopbutton" /></div>
								'.$item_quantity_status.'
								'.$item_question_out.'
							</div>
						</div>
					</div>
				</div>
				<div>&nbsp;</div>
				'.$img_more_out.'
				<div>'.$item_fulltext.'</div>
				<input type="hidden" value="'.$item_id.'" name="item_id" />
			</div>
		</form>
	';
}
elseif($shopSettings->getValue('view_item_card') == 2) // расширенный вид 2
{
	$out = '
		<form method="POST" action="/shop/basket">
			<div class="shop_item_extended_main">
				<h1 class="shop-item-title-3">'.$item_title.'</h1>
				<div class="shop_item_extended_table">
					<div class="shop_item_extended_tr">
						<div class="shop_item_extended_td">
							<div class="shop_item_extended_photo">
								'.$new_out.$discount_out.$item_photobig.'
							</div>
						</div>
						<div class="shop_item_extended_td">
							<div class="shop_item_extended_intro">
								'.$item_char.'
								<div class="shop_item_extended_introtext">'.$item_introtext.'</div>
								'.$price_old_out.$price_out.'
								<div class="shop-but" ><input type="submit" value="В корзину" class="shop_item_r_but" name="shopbutton" /></div>
								'.$item_quantity_status.'
								'.$item_question_out.'
							</div>
						</div>
					</div>
				</div>
				<div>&nbsp;</div>
				'.$img_more_out.'
				<div>'.$item_fulltext.'</div>
				<input type="hidden" value="'.$item_id.'" name="item_id" />
			</div>
		</form>
	';
}
else // обычный вид
{
	$out = '
		<form method="POST" action="/shop/basket">
		<h1 class="shop-item-title-2">'.$item_title.'</h1>
		<br/>
		<div class="shop-item-pic" style="position:relative;">'.$new_out.$discount_out.$item_photobig.'</div>
	';

	if($item_price != 0)
	{
		$out .= $price_old_out.$price_out;
	}

	$out .= '
		<div class="shop-but" ><input type="submit" value="В корзину" class="shop-button" name="shopbutton" /></div>
		'.$item_quantity_status.'
		'.$item_question_out.'
		<br/>
		<div>'.$item_char.'</div>
		'.$img_more_out.'
		<div>'.$item_introtext.'</div>
		<div>'.$item_fulltext.'</div>
		<input type="hidden" value="'.$item_id.'" name="item_id" />
		</form>
	';
}

if($frontend_edit == 1){echo '<div class="edit_mode" data-type="com_shop_item" data-id="'.$item_id.'">'.$out.'</div>';}
else {echo $out;}
?>
