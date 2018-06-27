<?php
defined('AUTH') or die('Restricted access');

if($d[3] == 'add'){include $root."/modules/circle/admin/add.php"; exit;}
if($d[3] == 'frontend_update'){include $root."/modules/circle/admin/frontend_update.php"; exit;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;}

if ($d[4] == 'update'){include $root."/modules/circle/admin/update.php";}
else {include $root."/modules/circle/admin/edit.php";}

?>