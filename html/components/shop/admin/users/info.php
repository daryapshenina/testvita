<?php
// Редактируем страницу
defined('AUTH') or die('Restricted access');


function a_com()
{
	global $db, $d;

	$id = intval($d[5]);


	$stmt = $db->prepare("
		SELECT u.id, u.email, u.date_visit, pu.price_type_id, pt.name price_name, o.id order_id, o.sum, o.date_order 
		FROM com_account_users u 
		LEFT JOIN com_shop_price_user pu ON pu.user_id = u.id
		LEFT JOIN com_shop_price_type pt ON pt.id = pu.price_type_id		
		LEFT JOIN com_shop_orders o ON o.user_id = u.id 
		WHERE u.id = :id
	");
	$stmt->execute(array('id' => $id));


	$out = '';
	while($u = $stmt->fetch())
	{
		$out .= '<tr><td>'.$u['order_id'].'</td><td><a class="contextmenu_shop_users" data-id="'.$u['id'].'" href="">'.$u['sum'].'</a></td><td></td><td>'.$u['date_order'].'</td></tr>';
		if(empty($u['price_type_id'])){$price_out = 'Основная цена';}else{$price_out = $u['price_name'];}
	}


	echo '
	<h1>Информация о пользователе '.$u['email'].'</h1>
	<table class="admin_table_2">
		<tr>
			<td style="width:100px;">Тип цен:</td>
			<td><b>'.$price_out.'</b></td>
		</tr>
	</table>
	';


	echo '
	<h1>Заказы</h1>
	<table class="admin_table even_odd">
		<tr>
			<th style="width:50px;">ID</th>
			<th style="width:200px;">Сумма заказа</th>
			<th></th>
			<th style="width:150px;">Дата заказа</th>
		</tr>
	'.$out.'</table>';

} // конец функции

?>