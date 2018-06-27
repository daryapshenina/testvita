<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/timer/frontend/timer.css');
$head->addFile('/modules/timer/frontend/timer.js');
if($frontend_edit == 1){$head->addFile('/modules/timer/frontend/edit.js');}
?>