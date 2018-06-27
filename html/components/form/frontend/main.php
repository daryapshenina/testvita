<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

if($frontend_edit == 1){$head->addFile('/components/form/frontend/edit.js');}

if ($d[1] == "mail" ){include("components/form/frontend/form_mail.php");} // отправка формы из компонента
else if ($d[1] == "mod_mail" ){include("components/form/frontend/form_mod_mail.php");} // отправка формы
else {include("components/form/frontend/form_output.php");} // вывод формы

// ================================================================================================
// функция подключения таблицы стилей компонента
$head->addFile('/components/form/frontend/tmp/style.css');

?>