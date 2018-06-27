<?php
defined('AUTH') or die('Restricted access');

// Получаем массив товаров в заказе
$items = Orders::getItems();

$items_out = '';
$items_count = 0;
$summa = 0;

foreach($items as $key => $item)
{
	$item['title'] = replace_quotes($item['title']);
	
	$items_count += $item['quantity'];
	if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
	$summa += $item['price'] * $item['quantity'];

	$price = number_format($item['price'], 0, '', ' ');
	if($item['price'] == 0){$price = '';}

	if(intval($item['quantity']) == $item['quantity']){$item['quantity'] = intval($item['quantity']);}

	$items_arr_js[$item['id']]['quantity'] = $item['quantity'];
	$items_arr_js[$item['id']]['price'] = $item['price'];

	if(!is_file($root."/components/shop/photo/".$item['photo']))
	{
		$basket_item_photo = '<img src=&quot;/modules/cart/frontend/img/no-photo.png&quot; />';
	}
	else
	{
		$basket_item_photo = '<img width=&quot;50&quot; src=&quot;/components/shop/photo/'.$item['photo'].'&quot; />';
	}

	$items_out .= '<tr id=&quot;mod_tr_'.$item['item_id'].'&quot;>';
	$items_out .= '<td style=&quot;width:50px;&quot;><a target=&quot;_blank&quot; href=&quot;/shop/item/'.$item['item_id'].'&quot;>'.$basket_item_photo.'</a></td>';
	$items_out .= '<td style=&quot;width:10px;&quot;></td>';
	$items_out .= '<td><a target=&quot;_blank&quot; href=&quot;/shop/item/'.$item['item_id'].'&quot;>'.$item['title'].'</a><br><span class=&quot;mod_cart_price&quot;>'.$price.'</span> '.$shopSettings->currency.' x <span class=&quot;mod_cart_quantity&quot;>'.$item['quantity'].'</span> шт.</td>';
	$items_out .= '<td id=&quot;mod_td_'.$item['id'].'&quot; style=&quot;width:10px;&quot;><img onclick=&quot;basket_delete_ajax(\\\'mod\\\','.$item['id'].');&quot; data-id=&quot;'.$item['id'].'&quot; src=&quot;/components/shop/frontend/tmp/images/delete.png&quot; alt=&quot;delete&quot; class=&quot;mod_cart_delete&quot;/></td>';
	$items_out .= '</tr>';
}

$summa  = number_format($summa, 0, '', ' ');

if(isset($items_arr_js) > 0){$script = '<script type="text/javascript">mod_cart_items = eval('.json_encode($items_arr_js).');</script>';}else{$script = '';}

$content = '<table id=&quot;mod_cart_table_popup&quot;><tbody><tr><td id=&quot;mod_cart_table_popup_title&quot; colspan=&quot;6&quot;><span id=&quot;mod_cart_table_popup_title_text&quot;>'.$items_count.'</span> '.LANG_M_CART_ITEMS.'<br />'.LANG_M_CART_TOTALING.' <span id=&quot;mod_cart_table_popup_title_itog&quot;>'.$summa.'</span> '.$shopSettings->currency.'</td></tr>';
$content .= $items_out;
$content .= '</tbody></table><table id=&quot;mod_cart_panel&quot;><tr><td id=&quot;mod_cart_table_popup_title&quot;><a id=&quot;mod_cart_go_cart&quot; href=&quot;/shop/basket&quot;>'.LANG_M_CART_CHECKOUT.'</a></td></tr></table>';

// Заголовок модуля
$out = '
	'.$script.'
	<div id="mod_cart" onclick="DAN_popup(\'mod_cart\', \'\', 0, 300, 1, \'alignright\', \''.$content.'\');">
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
				<td style="width:10px;">&nbsp;</td>
			</tr>
		</table>
	</div>
';

if(isset($m['module'])){$out = '<div id="mod_cart_main">'.$out.'</div>';} // Признак того, что вызов идёт через модуль, т.к. без него - вызов идёт из /components/shop/frontend/section/basket_add.php

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_cart" data-id="'.$m['id'].'">'.$out.'</div>';}
else {echo $out;}


?>