<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

$SQL = $db->query('SELECT id FROM com_shop_item');
echo $SQL->rowCount();

exit();
