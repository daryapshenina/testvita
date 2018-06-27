<?php
// DAN 2013
// выводит содержимое товара в разделе.
defined('AUTH') or die('Restricted access');

echo
'
<form method="POST" action="/shop/basket/add">
<input type="hidden" name="itemid" value="'.$item_id.'" />
<table class="ramka">
	<tr>
		<td width="'.$shopSettings->getValue('x_small').'" height="'.$shopSettings->getValue('y_small').'">'.$item_photo.'</td>
		<td>
			<div><a class="shop-item-title-link" href="/shop/item/'.$item_id.'">'.$item_title.'</a></div>
			<div>'.$item_introtext.'</div>
		</td>
		<td width="100">
			<div class="shop-item-price">'.$item_price.' <span class="rub">'.$shopSettings->getValue('currency').'</span></div>
			<div class="shop-but" ><input type="submit" value="В корзину" class="shop-button" name="shopbutton" /></div>
		</td>
	</tr>
</table>
</form>
<br/>
';

?>
