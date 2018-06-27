<?php
defined('AUTH') or die('Restricted access');

// условие - отменить
if ($admin_d1 == "chat" && $admin_d2 == ""){include("administrator/chat/admin/main.php");}
elseif ($admin_d2 == "settings"){include("administrator/chat/admin/settings.php");}
else {include("administrator/chat/admin/main.php");}

// Подключаем стили
$head->addFile('/administrator/chat/admin/chat_style.css');

?>