<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}

if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/price_type"); exit;}

$id = intval($d[5]);
$price_type = trim(htmlspecialchars($_POST["price_type"]));

if($price_type != '')
{
	$stmt = $db->prepare("UPDATE com_shop_price_type SET name = :name WHERE id = :id");
	$stmt->execute(array('name' => $price_type, 'id' => $id));
}
else
{
	function a_com()
	{
		echo 'Поле "Тип цены - не заполнено';
	} 	
}


if($bt_prim == 'Применить'){Header ("Location: /admin/com/shop/price_type/edit/".$id); exit;}
else {Header ("Location: /admin/com/shop/price_type"); exit;}

?>