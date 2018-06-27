<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/price_type/tmp/all.css');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_shop_price_type";
		var contextmenu_shop_price_type = [
			["admin/com/shop/price_type/add", "contextmenu_add", "Добавить тип цен"],
			["admin/com/shop/price_type/edit", "contextmenu_edit", "Редактировать"],			
			["admin/com/shop/price_type/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_shop_price_type);
	});
</script>
');

function a_com()
{
	global $db;

	$stmt = $db->query("SELECT * FROM com_shop_price_type WHERE 1");

	$out = '';
	$i = 0;
	while($t = $stmt->fetch())
	{
		$i++;
		$out .= '<tr><td style="width:50px;">'.$i.'</td><td><a class="contextmenu_shop_price_type" data-id="'.$t['id'].'" href="/admin/com/shop/price_type/edit/'.$t['id'].'">'.$t['name'].'</a></td></tr>';
	}

	echo '
	<h1>Типы цен</h1>
	<table class="admin_table_2">
		<tr>
			<td style="width:200px;"><a class="price_type_add" href="/admin/com/shop/price_type/add">Добавить тип цен</a></td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<table class="admin_table even_odd">
		<tr>
			<th style="width:50px;"></th>
			<th>Типы цен</th>
		</tr>
	'.$out.'</table>';
}

?>