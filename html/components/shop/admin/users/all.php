<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/users/tmp/all.css');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_shop_users";
		var contextmenu_shop_users = [
			["admin/com/shop/users/info", "contextmenu_info", "Информация"],
			["admin/com/shop/users/price_edit", "contextmenu_edit", "Редактировать тип цен"],
		];
		contextmenu(class_name, contextmenu_shop_users);
	});
</script>
');

function a_com()
{
	global $db;

	$stmt = $db->query("SELECT u.id, u.email, u.date_visit, pu.price_type_id, pt.name FROM com_account_users u LEFT JOIN com_shop_price_user pu ON pu.user_id = u.id LEFT JOIN com_shop_price_type pt ON pt.id = pu.price_type_id  WHERE u.status = '1'");

	$out = '';
	$i = 0;
	while($u = $stmt->fetch())
	{
		$i++;
		$out .= '<tr><td>'.$u['id'].'</td><td><a class="contextmenu_shop_users" data-id="'.$u['id'].'" href="/admin/com/shop/users/info/'.$u['id'].'">'.$u['email'].'</a></td><td>'.$u['name'].'</td><td></td><td>'.$u['date_visit'].'</td></tr>';
	}

	echo '
	<h1>Пользователи</h1>
	<table class="admin_table even_odd">
		<tr>
			<th style="width:50px;">ID</th>
			<th style="width:200px;">Пользователь</th>
			<th style="width:150px;">Тип цен</th>
			<th></th>
			<th style="width:150px;">Последний визит</th>
		</tr>
	'.$out.'</table>';
}

?>