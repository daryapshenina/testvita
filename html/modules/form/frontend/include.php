<?php
defined('AUTH') or die('Restricted access');


switch($m['p10'])
{
	case 1: $head->addFile('/modules/form/frontend/style.css'); break;
	case 2: $head->addFile('/modules/form/frontend/style_2.css'); break;
	case 3: $head->addFile('/modules/form/frontend/style_3.css'); break;
}

if($frontend_edit == 1){$head->addFile('/modules/form/frontend/edit.js');}
?>