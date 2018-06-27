<?php
// определяет действие внутри компонента

defined('AUTH') or die('Restricted access');

if ($admin_d3 == "add"){include("components/page/admin/add.php");} // Добавить страницу компонента - форма ввода 
elseif ($admin_d3 == "insert"){include("components/page/admin/insert.php");} // Вставить новую страницу
// elseif ($admin_d3 == ""){include("components/page/admin/all.php");} // Вывести все страницы (компонент страниа)
elseif ($admin_d3 == ""){include("components/page/admin/help.php");} // Общее описание
elseif ($admin_d3 == "update"){include("components/page/admin/update.php");} // Заменить редактируемую страницу (главное меню)
elseif ($admin_d3 == "up"){include("components/page/admin/up.php");} // Поднять страницу
elseif ($admin_d3 == "down"){include("components/page/admin/down.php");} // Опустить страницу
elseif ($admin_d3 == "pub"){include("components/page/admin/pub.php");} // Опубликовать
elseif ($admin_d3 == "unpub"){include("components/page/admin/unpub.php");} // Скрыть
elseif ($admin_d3 == "delete"){include("components/page/admin/delete.php");} // Удалить страницу

else {include("components/page/admin/edit.php");} // Редактировать страницу (главное меню)
?>