<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/search/frontend/mod_search.js');
$head->addFile('/modules/search/frontend/style.css');
if($frontend_edit == 1){$head->addFile('/modules/search/frontend/edit.js');}
?>