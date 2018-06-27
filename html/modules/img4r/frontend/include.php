<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/img4r/frontend/style.css');
if($frontend_edit == 1){$head->addFile('/modules/img4r/frontend/edit.js');}
?>