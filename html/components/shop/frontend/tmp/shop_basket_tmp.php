<?php
// DAN 2013
// выводит товар в шаблоне корзины
defined('AUTH') or die('Restricted access');

// шапка корзины
echo
'
<form method="POST" action="/shop/basket/client">
	<div class="shop-item-title-2">'.LANG_BASKET.'</div>
	<div class="basket-item">
			<table class="basket-item-tab">
				<tr>
					<td class="basket-item-title"><b>'.LANG_PRODUCT_NAME.'</b></td>
					<td class="basket-item-klv"><b>'.LANG_COUNT.'</b></td>
					<td class="basket-item-price"><b>'.LANG_PRICE.'</b></td>
					<td class="basket-item-summa"><b>'.LANG_SUM.'</b></td>
					<td class="basket-item-delete"><b>'.LANG_DELETE.'</b></td>
				</tr>
'.$basket_middle.'
			</table>

		<div class="w-sep"></div>
		<div class="shop-item-price-3">'.LANG_TOTAL.': <span id="summa">'.LANG_SUM.'</span> '.$shopSettings->getValue('currency').'</div>
		<div class="shop-but" ><input type="submit" value="'.LANG_CHECKOUT.'" class="shop-button" name="shopbutton"/></div>
	</div>
	<div class="main-right-header-3"></div>
	<script type="text/javascript">
		new raschet();
	</script>
</form>
';

?>