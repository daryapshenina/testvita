<?php
// Редактируем страницу
defined('AUTH') or die('Restricted access');


function a_com()
{
	global $db, $d;

	$id = intval($d[5]);

	$stmt_u = $db->prepare("SELECT u.id, u.email, pu.price_type_id, pt.name FROM com_account_users u LEFT JOIN com_shop_price_user pu ON pu.user_id = u.id LEFT JOIN com_shop_price_type pt ON pt.id = pu.price_type_id WHERE u.id = :id");	
	$stmt_u->execute(array('id' => $id));
	$u = $stmt_u->fetch();

	$option = '<option value="0">Основная цена</option>';

	$sql_t = $db->query("SELECT * FROM com_shop_price_type");
	while($t = $sql_t->fetch())
	{
		if($t['id'] == $u['price_type_id']){$sel = 'selected';}else{$sel = '';}
		$option .= '<option value="'.$t['id'].'" '.$sel.'>'.$t['name'].'</option>';
	}


	echo '
	<h1>Тип цен для пользователя '.$u['email'].'</h1>
	<form enctype="multipart/form-data" method="POST" action="/admin/com/shop/users/price_update/'.$u['id'].'">
	<table class="admin_table_2">
		<tr>
			<td style="width:100px;">Тип цен</td>
			<td><select name="price_type" class="input">'.$option.'</select></td>
		</tr>
	</table>
	<br/>
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
	<br/>
	&nbsp;
	</form>
	';

} // конец функции

?>