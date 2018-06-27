<?php
defined('AUTH') or die('Restricted access');
// p1 === Размер в процентах (0) / фиксированный (1)
// p2 === Высота плашки в пикселях
// p3 === Ширина плашки фиксированная
// p4 === Ширина плашки в % >>> ПК;ноутбук;планшет;смартфон
// p5 === Отступ x;y
// p6 === Тип кнопки >>> цветная (0) / прозрачная с белой окантовкой (1)
// p7 === Текст кнопки
// p8 === Цвет кнопки
// p9 === Цвет подложки
// p10 === Прозрачность

include_once __DIR__.'/lang/'.LANG.'.php';

if($admin_d3 == 'add'){include $root."/modules/flat_shadow_button/admin/add.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == LANG_M_FLAT_SHADOW_BUTTON_CANCEL){Header ("Location: /admin/modules"); exit;} 

if ($d[4] == 'update'){include $root."/modules/flat_shadow_button/admin/update.php";}
else {include $root."/modules/flat_shadow_button/admin/edit.php";}

?>