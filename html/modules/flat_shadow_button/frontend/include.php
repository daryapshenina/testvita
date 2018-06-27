<?php
defined('AUTH') or die('Restricted access');

if($frontend_edit == 1){$head->addFile('/modules/flat_shadow_button/frontend/edit.js');}

$head->addFile('/modules/flat_shadow_button/frontend/style.css');
$head->addFile('/modules/flat_shadow_button/frontend/flat_shadow_button.js');

?>
