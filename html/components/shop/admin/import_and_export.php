<?php
// DAN 2010
// Редактируем раздел

defined('AUTH') or die('Restricted access');

$task = $admin_d4; 

if (!isset($task) || $task == ""){include("components/shop/admin/import_and_export_task.php");}

// импорт из excel
elseif ($task == "import_excel"){include("components/shop/admin/import_excel.php");}
elseif ($task == "import_excel_v"){include("components/shop/admin/import_excel_v.php");}
elseif ($task == "import_excel_12"){include("components/shop/admin/import_excel_12.php");}
elseif ($task == "import_excel_13"){include("components/shop/admin/import_excel_13.php");}
elseif ($task == "import_excel_22"){include("components/shop/admin/import_excel_22.php");}
elseif ($task == "import_excel_23"){include("components/shop/admin/import_excel_23.php");}

// экспорт в excel
elseif ($task == "export_excel"){include("components/shop/admin/export_excel.php");}
elseif ($task == "export_excel_start"){include("components/shop/admin/export_excel_start.php");}

else {include("components/shop/admin/import_and_export_task.php");}

?>