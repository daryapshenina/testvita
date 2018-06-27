<?php
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$s['x_small'] = intval($_POST['x_small']);
$s['y_small'] = intval($_POST['y_small']);
$s['x_big'] = intval($_POST['x_big']);
$s['y_big'] = intval($_POST['y_big']);
$s['resize_method'] = intval($_POST['resize_method']);
$s['quantity'] = intval($_POST['quantity']);
$s['type'] = intval($_POST['type']);
$s['sorting_items'] = intval($_POST['sorting_items']);
$s['section_description'] = intval($_POST['section_description']);

if(isset($_POST["none"])){$none = $_POST["none"];} else {$none = '';}

// Условие - отменить
if ($none == "Отменить"){Header ("Location: /admin/com/photo"); exit;}
else
{
	foreach($s as $name=>$value)
	{
		$stmt_update = $db->prepare("UPDATE com_photo_settings SET value = :value WHERE name = :name");
		$stmt_update->execute(array('name'=>$name, 'value'=>$value));
	}
} // конец условия заполненного пункта меню

Header ("Location: /admin/com/photo"); exit;

?>