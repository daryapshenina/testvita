<?php
defined('AUTH') or die('Restricted access');

include $_SERVER['DOCUMENT_ROOT']."/db.php";

$db->query('DELETE FROM com_shop_char_name');
$db->query('DELETE FROM com_shop_char');
$db->query('DELETE FROM com_shop_filter');

exit();
