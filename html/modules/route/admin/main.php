<?php
defined('AUTH') or die('Restricted access');

// p1 === Высота карты
// p2 === y
// p3 === x
// p7 === 0 / 1 >>> Размер модуля в процентах (0) / фиксированный (1)
// p8 ===  Ширина плашки в % >>> ПК;ноутбук;планшет;смартфон
// p9  === Margin x;y
// p10  === Padding x;y

if($admin_d3 == 'add'){include $root."/modules/route/admin/add.php"; exit;}
if($admin_d3 == 'frontend_update'){include $root."/modules/route/admin/frontend_update.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none != ''){Header ("Location: /admin/modules"); exit;}

if ($d[3] == 'update'){include $root."/modules/route/admin/update.php";}
else {include $root."/modules/route/admin/edit.php";}
?>