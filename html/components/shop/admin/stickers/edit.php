<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/stickers/style.css');

function a_com()
{
	global $db, $shopSettings;

	echo '
		<h1>Стикеры:</h1>
		<form method="POST" action="/admin/com/shop/stickers/update">
		<table class="admin_table">
			<tr>
				<th style="width:50px;"></td>
				<th style="width:200px;">Тип стикера</td>
				<th>Отображаемый текст на стикере</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Сопутствующие товары</td>
				<td><input type="text" name="related" class="input" value="'.$shopSettings->related_items.'" size="50"></td>
			</tr>			
			<tr>
				<td>&nbsp;</td>
				<td>Новинка</td>
				<td><input type="text" name="new" class="input" value="'.$shopSettings->sticker_new.'" size="50"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Распродажа</td>
				<td><input type="text" name="sale" class="input" value="'.$shopSettings->sticker_sale.'" size="50"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Хит</td>
				<td><input type="text" name="hit" class="input" value="'.$shopSettings->sticker_hit.'" size="50"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Рейтинг</td>
				<td><input type="text" name="rating" class="input" value="'.$shopSettings->sticker_rating.'" size="50"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Под заказ</td>
				<td><input type="text" name="order" class="input" value="'.$shopSettings->sticker_order.'" size="50"></td>
			</tr>			
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
		<br/>
		&nbsp;
		</form>
	';
}
?>