<?php
// DAN 2010
// Настройки сайта
defined('AUTH') or die('Restricted access');

if(isset($_POST["none"])){$none = $_POST["none"];} else {$none = '';} // кнопка 'Отменить'

// условие - отменить
if ($admin_d2 == "update" && $none == "Отменить"){Header ("Location: /admin/"); exit;}
elseif ($admin_d2 == "update"){include("administrator/settings/update.php");}
elseif ($admin_d2 == "block"){include("administrator/settings/block.php");}
elseif ($admin_d2 == "addmod"){include("administrator/settings/addmod.php");}
else {include("administrator/settings/edit.php");}
?>