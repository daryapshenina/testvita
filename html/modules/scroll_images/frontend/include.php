<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/scroll_images/frontend/styles.css');
if($frontend_edit == 1){$head->addFile('/modules/scroll_images/frontend/edit.js');}
?>
