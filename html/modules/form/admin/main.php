<?php
defined('AUTH') or die('Restricted access');

// p1 pub_1 text_2 
// p2 text - placeholder
// p3 pub_2 - textarea_2
// p4 textarea_1
// p5 pub_3 text_3
// p6 text_3
// p7 pub_4 загрузка файла 
// p8 pub_5 капча
// p9  === Ширина плашки в % >>> ПК;ноутбук;планшет;смартфон
// p10  === Эффект появления

include_once __DIR__.'/lang/'.LANG.'.php';

if($d[3] == 'add'){include $root."/modules/form/admin/add.php"; exit;}
if($d[3] == 'frontend_update'){include $root."/modules/form/admin/frontend_update.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == LANG_M_FORM_CANCEL){Header ("Location: /admin/modules"); exit;}

if ($d[4] == 'update'){include $root."/modules/form/admin/update.php";}
else {include $root."/modules/form/admin/edit.php";}

?>