<?php
// DAN 2010
// Редактируем раздел

defined('AUTH') or die('Restricted access');

$task = $admin_d4; 

if (!isset($task) || $task == ""){include("components/shop/admin/import_task.php");}
elseif ($task == "excel"){include("components/shop/admin/import_excel.php");}
elseif ($task == "excel_v"){include("components/shop/admin/import_excel_v.php");}
elseif ($task == "excel_12"){include("components/shop/admin/import_excel_12.php");}
elseif ($task == "excel_13"){include("components/shop/admin/import_excel_13.php");}
elseif ($task == "excel_22"){include("components/shop/admin/import_excel_22.php");}
elseif ($task == "excel_23"){include("components/shop/admin/import_excel_23.php");}
else {include("components/shop/admin/import_task.php");}

?>