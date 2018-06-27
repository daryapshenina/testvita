<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}

if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/users"); exit;}

$id = intval($d[5]);
$price_type = intval($_POST["price_type"]);

// Ищем этого пользователя в БД
$stmt = $db->prepare("SELECT id FROM com_shop_price_user WHERE user_id = :user_id LIMIT 1");
$stmt->execute(array('user_id' => $id));


if($stmt->rowCount() == 0) // Пользователя с типами цен - нет
{
	$stmt = $db->prepare("INSERT INTO com_shop_price_user SET price_type_id = :price_type_id, user_id = :user_id");
	$stmt->execute(array('price_type_id' => $price_type, 'user_id' => $id));
}
else // Пользователь уже внесён в БД с типами цен
{
	$stmt = $db->prepare("UPDATE com_shop_price_user SET price_type_id = :price_type_id WHERE user_id = :user_id LIMIT 1");
	$stmt->execute(array('price_type_id' => $price_type, 'user_id' => $id));
}


if($bt_prim == 'Применить'){Header ("Location: /admin/com/shop/users/price_edit/".$id); exit;}
else {Header ("Location: /admin/com/shop/users"); exit;}

?>