<?php
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
					<th class="basket-item-title"><b>'.LANG_PRODUCT_NAME.'</b></th>
					<th class="basket-item-klv" data-text="Заказ"><b>'.LANG_COUNT.'</b></th>
					<th class="basket-item-price"><b>'.LANG_PRICE.'</b></th>
					<th class="basket-item-sum"><b>'.LANG_SUM.'</b></th>
					<th class="basket-item-delete"><b>'.LANG_DELETE.'</b></th>
				</tr>
'.$items_out.'
			</table>

		<div class="w-sep"></div>
		<div class="basket-item-summa">'.LANG_TOTAL.': <span id="com_summa">'.LANG_SUM.'</span> '.$shopSettings->currency.'</div>
		<div class="shop-but" ><input type="submit" value="'.LANG_CHECKOUT.'" class="button_green_light" name="shopbutton"/></div>
	</div>
	<div class="main-right-header-3"></div>
	<script type="text/javascript">
		raschet();
	</script>
</form>
';

?>