<?php
// Редактируемый модуль
defined('AUTH') or die('Restricted access');

// p1  === Размер в процентах (0) / фиксированный (1)
// p2  === Высота плашки в пикселях
// p3  === Ширина плашки фиксированная
// p4  === Ширина плашки в % >>> ПК;ноутбук;планшет;смартфон
// p5  === Margin x;y
// p6  === Padding x;y
// p7  === Фон цветной 1 - да 0 - нет
// p8  === Цвет фона
// p9  === Эффект появления
// p10 === Автовыравнивание

include_once __DIR__.'/lang/'.LANG.'.php';

if($admin_d3 == 'add'){include $root."/modules/editor/admin/add.php"; exit;}
if($admin_d3 == 'frontend_update'){include $root."/modules/editor/admin/frontend_update.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == LANG_M_EDITOR_CANCEL){Header ("Location: /admin/modules"); exit;}

if ($d[4] == 'update'){include $root."/modules/editor/admin/update.php";}
else {include $root."/modules/editor/admin/edit.php";}

?>