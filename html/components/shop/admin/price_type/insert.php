<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/price_type"); exit;}

$price_type = trim(htmlspecialchars($_POST["price_type"]));

if($price_type != '')
{
	$stmt = $db->prepare("INSERT INTO com_shop_price_type SET name = :name");
	$stmt->execute(array('name' => $price_type));
}
else
{
	function a_com()
	{
		echo 'Поле "Тип цены - не заполнено';
	} 	
}

Header ("Location: /admin/com/shop/price_type"); exit;

?>