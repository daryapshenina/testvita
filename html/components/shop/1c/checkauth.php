<?php
// Предварительная установка
defined('AUTH') or die('Restricted access');

// Обнуляем значения в базе данных процесса загрузки из 1с
$stmt_process = $db->query('UPDATE com_shop_1c_processing SET import_number = \'0\', import_sum = \'0\', offers_number = \'0\', offers_sum = \'0\', steps = \'0\', chars = \'0\' WHERE id = \'1\' ');	

// стираем старые xml файлы
if (file_exists($root.$dir.'import.xml')){unlink($root.$dir.'import.xml');}
if (file_exists($root.$dir.'offers.xml')){unlink($root.$dir.'offers.xml');}
if (file_exists($root.$dir.'import0_1.xml')){unlink($root.$dir.'import0_1.xml');}
if (file_exists($root.$dir.'offers0_1.xml')){unlink($root.$dir.'offers0_1.xml');}

// ответ для 1с
echo "success";
exit;
?>