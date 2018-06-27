<?php
// DAN 2012
// определяет действие внутри компонента

defined('AUTH') or die('Restricted access');

if ($admin_d3 == "" ||$admin_d3 == "edit" ){include("components/form/admin/form_edit.php");} // редактирование формы
elseif ($admin_d3 == "update"){include("components/form/admin/form_update.php");} // обновить занные

elseif ($admin_d3 == "up"){include("components/form/admin/form_up.php");} // поднять пункт в меню
elseif ($admin_d3 == "down"){include("components/form/admin/form_down.php");} // опустить пункт в меню
elseif ($admin_d3 == "pub"){include("components/form/admin/form_pub.php");} // показать форму
elseif ($admin_d3 == "unpub"){include("components/form/admin/form_unpub.php");} // скрыть форму

else {include("components/form/admin/form_edit.php");} // редактирование формы
?>