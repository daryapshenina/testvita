<?php
defined('AUTH') or die('Restricted access');

include_once $root."/components/shop/classes/Orders.php";

$id = $d[3];

// удаляем товар
$items = Orders::deleteItems($id);

$m['p1'] = 1;

include_once $root."/modules/cart/frontend/main.php";

exit;
?>