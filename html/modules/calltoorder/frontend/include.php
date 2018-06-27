<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/calltoorder/frontend/dan_cto.css');
if(LANG == 'ru') $head->addFile('/modules/calltoorder/frontend/dan_cto.js');
	else $head->addFile('/modules/calltoorder/frontend/dan_cto_'.LANG.'.js');
$head->addFile('/js/vanilla-masker.min.js');

if($frontend_edit == 1){$head->addFile('/modules/calltoorder/frontend/edit.js');}
?>