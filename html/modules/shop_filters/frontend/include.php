<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/shop_filters/frontend/style.css');

if($frontend_edit == 1){$head->addFile('/modules/shop_filters/frontend/edit.js');}

?>