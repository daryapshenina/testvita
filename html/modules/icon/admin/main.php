<?php
defined('AUTH') or die('Restricted access');

// content === иконка UTF-8
// content_2 === Текст
// p1  === Тип плашки
// p2  === Размер модуля в процентах (0) / фиксированный (1)
// p3  === Ширина плашки в % >>> ПК;ноутбук;планшет;смартфон
// p4  === Ширина плашки;Высота плашки в пикселях, фиксированная.
// p5  === Margin x;y
// p6  === Padding x;y
// p7  === Размер иконки
// p8  === Цвет
// p9  === Эффект появления
// p10 === Автовыравнивание

include_once __DIR__.'/lang/'.LANG.'.php';

if($d[3] == 'add'){include $root."/modules/icon/admin/add.php"; exit;}
if($d[3] == 'frontend_update'){include $root."/modules/icon/admin/frontend_update.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none != ''){Header ("Location: /admin/modules"); exit;}

if ($d[4] == 'update'){include $root."/modules/icon/admin/update.php";}
else {include $root."/modules/icon/admin/edit.php";}

?>