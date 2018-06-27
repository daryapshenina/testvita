<?php
defined('AUTH') or die('Restricted access');

// Получаем массив товаров в заказе
$items = Orders::getItems();


$items_count = 0;
$summa = 0;

foreach($items as $key => $item)
{
	$items_count += $item['quantity'];
	if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
	$summa += $item['price'] * $item['quantity'];

	$price = number_format($item['price'], 0, '', ' ');
	if($item['price'] == 0){$price = '';}

	if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}

	$items_arr_js[$item['id']]['quantity'] = $item['quantity'];
	$items_arr_js[$item['id']]['price'] = $item['price'];
}

$summa  = number_format($summa, 0, '', ' ');

if(isset($items_arr_js) > 0){$script = '<script type="text/javascript">mod_cart_items = eval('.json_encode($items_arr_js).');</script>';}else{$script = '';}


// Заголовок модуля
$out = '
	'.$script.'
	<div id="mod_cart">
		<table id="mod_cart_table">
			<tr>
				<td style="width:10px;">&nbsp;</td>
				<td rowspan="2" id="mod_cart_img"></td>
				<td style="width:10px;">&nbsp;</td>				
				<td style="height:20px;">'.LANG_M_CART_IN_CART.' <span id="mod_cart_quantity">'.$items_count.'</span> '.LANG_M_CART_ITEMS.'</td>
				<td style="width:10px;">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>'.LANG_M_CART_TOTALING.' <span id="mod_cart_summa">'.$summa.'</span> '.$shopSettings->currency.'</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
';

if(isset($m['module'])){$out = '<a href="/shop/basket" id="mod_cart_main">'.$out.'</a>';} // Признак того, что вызов идёт через модуль, т.к. без него - вызов идёт из /components/shop/frontend/section/basket_add.php

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_cart" data-id="'.$m['id'].'">'.$out.'</div>';}
else {echo $out;}


?>