<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/slider_skitter/frontend/styles.css');
$head->addFile('/modules/slider_skitter/frontend/jquery.skitter.min.js');
$head->addFile('/modules/slider_skitter/frontend/jquery.easing.1.3.js');
$head->addFile('/modules/slider_skitter/frontend/jquery.animate-colors-min.js');
if($frontend_edit == 1){$head->addFile('/modules/slider_skitter/frontend/edit.js');}
?>