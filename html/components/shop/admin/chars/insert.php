<?php
// DAN 2015
defined('AUTH') or die('Restricted access');

$head->addFile('/components/shop/admin/chars/chars.css');

$name = trim(htmlspecialchars($_POST["name"]));
$unit = trim(htmlspecialchars($_POST["unit"]));
$type = trim(htmlspecialchars($_POST["type"]));
$ordering = intval($_POST["ordering"]);

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';} // кнопка 'Отменить'

$s = array("'", '"');
$name = str_replace($s,'`',$name);

$pattern = "/[^(a-z0-9а-я\_\-\.\\\ \(\)\`\/)]/iu";
$replacement = "";
$name = preg_replace($pattern, $replacement, $name);
$unit = preg_replace($pattern, $replacement, $unit);

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop/chars"); exit;}

// Вставляем данные в таблице "com_shop_char_name"
$stmt_char_name = $db->prepare("INSERT INTO com_shop_char_name SET name = :name, unit = :unit, type = :type, ordering = :ordering");
$stmt_char_name->execute(array('name'=>$name, 'unit'=>$unit, 'type'=>$type, 'ordering'=>$ordering));

if($bt_save == 'Сохранить'){Header ("Location: /admin/com/shop/chars"); exit;}
else {Header ("Location: /admin/com/shop/chars/edit/".$admin_d5); exit;}

?>