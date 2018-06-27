<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/ads/frontend/style.css');
if($frontend_edit == 1){$head->addFile('/modules/ads/frontend/edit.js');}
?>