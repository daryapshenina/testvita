<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/modules/route/frontend/style.css');
$head->addFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU&.js');
$head->addFile('/modules/route/frontend/route.js');

if($frontend_edit == 1){$head->addFile('/modules/route/frontend/edit.js');}
