<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/cart/frontend/styles.css');
$head->addFile('/components/shop/frontend/basket/tmp/basket.js');

if($frontend_edit == 1){$head->addFile('/modules/cart/frontend/edit.js');}

?>