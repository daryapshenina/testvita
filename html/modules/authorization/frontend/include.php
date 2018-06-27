<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/authorization/frontend/style.css');
$head->addFile('/lib/css/account/form.css');
$head->addFile('/lib/css/font-awesome/css/font-awesome.min.css');

if($frontend_edit == 1){$head->addFile('/modules/authorization/frontend/edit.js');}
?>