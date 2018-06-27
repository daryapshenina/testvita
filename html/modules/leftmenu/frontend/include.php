<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/leftmenu/frontend/leftmenu.css');
if($frontend_edit == 1){$head->addFile('/modules/leftmenu/frontend/edit.js');}
?>
