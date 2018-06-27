<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/components/shop/admin/classes/Shop.php";

Shop\deleteAllChars();

exit();
