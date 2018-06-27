<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/jumptotop/frontend/style.css');
$head->addFile('/modules/jumptotop/frontend/script.js');

if($frontend_edit == 1){$head->addFile('/modules/jumptotop/frontend/edit.js');}
?>
