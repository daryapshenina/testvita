<?php
defined('AUTH') or die('Restricted access');

if($frontend_edit == 1){$head->addFile('/modules/flat_rotate/frontend/edit.js');}

switch($m['effect']){
    case 1: $head->addFile('/modules/flat_rotate/frontend/style_1.css'); break;
    case 2: $head->addFile('/modules/flat_rotate/frontend/style_2.css'); break;	
	default: 
		$head->addFile('/modules/flat_rotate/frontend/style_0.css');
		$head->addFile('/modules/flat_rotate/frontend/flat_rotate.js');
}

?>