<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/editor/frontend/style.css');

if($frontend_edit == 1){$head->addFile('/modules/editor/frontend/edit.js');}

?>