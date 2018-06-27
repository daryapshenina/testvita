<?php
defined('AUTH') or die('Restricted access');

// p1 >>>    0 - случайный товар;   1 - последний товар
// p2 >>>    количество товаров
// p3 >>>    разделы   $_POST["prazdel"]
// p4 >>>    ссылка на раздел; если стоит "discount" - ведёт на скидки, "new" - на новинку,  "nit" - на хит
// p5 >>>    анкор ссылки
// p6 >>>    0 - все;   1 - скидка;   2 - новинки;   3 - скидки и новинки;   4 - хиты
// p7 >>>	 0 - выводить ссылку в подвале; 1 - заголовок модуля ссылкой
// p10 >>>   Тип модуля. 0 - стандартный, 1 - скроллер

include_once __DIR__.'/lang/'.LANG.'.php';

if($d[3] == 'add'){include $root."/modules/shop/admin/add.php"; exit;}
if($d[3] == 'frontend_update'){include $root."/modules/shop/admin/frontend_update.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none != ''){Header ("Location: /admin/modules"); exit;}

if ($d[3] == 'update'){include $root."/modules/shop/admin/update.php";}
else {include $root."/modules/shop/admin/edit.php";}


?>
