<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/shop/frontend/basket/tmp/basket.js');
$head->addFile('/components/shop/frontend/basket/tmp/basket.css');

include_once $root."/components/shop/classes/Orders.php";

// Для того, что бы не было добавление товара при обновлении страницы - перезапрашиваем её методом GET
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$item_id = $_POST['item_id'];
	if(isset($_POST['input_quantity'])){$input_quantity = $_POST['input_quantity'];} else {$input_quantity = 1;}
	if(isset($_POST['char'])){$chars_arr = $_POST['char'];} else{$chars_arr = '';}

	$chars = '';

	if(is_array($chars_arr))
	{
		foreach ($chars_arr as $key => $value)
		{
			$chars .= $key.': '.$value.'; ';
		}
	}
	
	// Добавляем товар к заказу.
	Orders::addItem($item_id, $input_quantity, $chars);
	Header ("Location: /shop/basket"); exit;
}

// Получаем массив товаров в заказе
$items = Orders::getItems();


function component()
{
	global $root, $domain, $shopSettings, $items;

	$items_out = '';
	foreach($items as $key => $item)
	{
		if($item['price'] < 0 || $item['price'] > 999999999) $item['price'] = 0;
		$price = number_format($item['price'], 0, '', ' ');
		if($item['price'] == 0){$price = '';}

		$items_out .= '
		<tr id="com_tr_'.$item['id'].'">
			<td class="basket-item-title"><a href="/shop/item/'.$item['item_id'].'" target="_blank"><img src="/components/shop/photo/'.$item['photo'].'" class="basket-item-img"></a><a href="/shop/item/'.$item['item_id'].'" target="_blank">'.$item['title'].'</a> &nbsp; '.$item['chars'].'</td>
			<td class="basket-item-klv"><input name="quantity['.$item['id'].']" onkeyup="raschet()" class="input com_basket_quantity" type="text" size="3" value="'.$item['quantity'].'"><input type="hidden" name="item_id['.$item['item_id'].']" value="'.$item['item_id'].'" ></td>
			<td class="basket-item-price" data-text="Цена:"><span class="basketform" ><input type="hidden" class="com_basket_price" value="'.$item['price'].'"><span>'.$price.'</span></td>
			<td class="basket-item-sum" data-text="Сумма:"><span class="com_basket_sum">Сумма</span></td>
			<td id="com_td_'.$item['id'].'" class="basket-item-delete" data-text="Удалить:"><img onclick="basket_delete_ajax(\'com\','.$item['id'].');" class="order_items_delete" src="/components/shop/frontend/basket/tmp/images/delete.png" border="0" alt=""></td>
		</tr>
		';
	}

	if(count($items) > 0)
	{
		include_once $root."/components/shop/frontend/basket/tmp/basket.php";		
	}
	else
	{
		echo'
			<div class="main-right-header-1"></div>
			<div class="main-right-header-2">
				<div class="shop-item-title-2">'.LANG_BASKET_IS_EMPTY.'</div>
				<div class="basket-item">'.LANG_YOU_SHOULD_ADD_ITEMS.'</div>
			</div>
			';		
	}
}


?>