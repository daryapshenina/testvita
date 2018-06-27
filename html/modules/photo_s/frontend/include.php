<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/photo_s/frontend/style.css');
if($frontend_edit == 1){$head->addFile('/modules/photo_s/frontend/edit.js');}
?>