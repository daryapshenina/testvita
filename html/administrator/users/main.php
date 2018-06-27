<?php
// DAN 2010
// Настройки сайта
defined('AUTH') or die('Restricted access');

if(isset($_POST["none"])){$none = $_POST["none"];}else{$none = '';} // кнопка 'Отменить'

// условия
if ($admin_d2 == "update"){include("administrator/users/update.php");}
elseif ($admin_d2 == "adduser"){include("administrator/users/adduser.php");}
else {include("administrator/users/edit.php");}

?>