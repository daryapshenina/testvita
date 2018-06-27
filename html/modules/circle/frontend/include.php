<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/circle/frontend/style.css');
if($frontend_edit == 1){$head->addFile('/modules/circle/frontend/edit.js');}
?>