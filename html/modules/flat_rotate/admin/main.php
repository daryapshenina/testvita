<?php
defined('AUTH') or die('Restricted access');

// content - содержимое лицевой стороны
// content_2 - содержимое обратной стороны
// p1 ширина блока
// p2 высота блока
// p3 цвет
// p4 ссылка
// p5 margin_w
// p6 margin_h
// effect - эффект

include_once __DIR__.'/lang/'.LANG.'.php';

if($d[3] == 'add'){include $root."/modules/flat_rotate/admin/add.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == LANG_M_FLAT_ROTATE_CANCEL){Header ("Location: /admin/modules"); exit;}

if ($d[4] == 'update'){include $root."/modules/flat_rotate/admin/update.php";}
else {include $root."/modules/flat_rotate/admin/edit.php";}

?>