<?php
// DAN 2011
// Настройки сайта
defined('AUTH') or die('Restricted access');

if (!isset($admin_d2) || $admin_d2 == ''){include("administrator/upgrade/start.php");}
if ($admin_d2 == "upgrade"){include("administrator/upgrade/upgrade.php");}

?>