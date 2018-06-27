<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/exchangerates/frontend/styles.css');

if($frontend_edit == 1){$head->addFile('/modules/exchangerates/frontend/edit.js');}

?>