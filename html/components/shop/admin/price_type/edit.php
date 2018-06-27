<?php
// Редактируем страницу
defined('AUTH') or die('Restricted access');


function a_com()
{
	global $db, $d;

	$id = intval($d[5]);

	if($d[4] == 'add')
	{
		$h1 = 'Добавить тип цен';
		$form_act = 'insert';
		$price_type = '';
	}
	else
	{
		$h1 = 'Редактировать тип цен';
		$form_act = 'update/'.$id;

		$stmt = $db->prepare("SELECT * FROM com_shop_price_type WHERE id = :id");
		$stmt->execute(array('id' => $id));
		$t = $stmt->fetch();

		$price_type = $t['name'];
	}



	echo '
	<h1>'.$h1.'</h1>
	<form enctype="multipart/form-data" method="POST" action="/admin/com/shop/price_type/'.$form_act.'">
	<table class="admin_table_2">
		<tr>
			<td style="width:100px;">Тип цен</td>
			<td><input name="price_type" class="input" size="50" value="'.$price_type.'" requered></td>
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