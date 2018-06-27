<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/breadcrumbs/frontend/styles.css');
if($frontend_edit == 1){$head->addFile('/modules/breadcrumbs/frontend/edit.js');}

?>