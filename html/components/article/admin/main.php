<?php
// DAN 2014
// определяет действие внутри компонента
defined('AUTH') or die('Restricted access');

if ($admin_d3 == ""){include("components/article/admin/help.php");} // Вывести всё содержимое
elseif ($admin_d3 == "articleedit" || $admin_d3 == "all"){include("components/article/admin/article_edit.php");} // Редактировать главную стр. архива статей.
elseif ($admin_d3 == "updatearticle"){include("components/article/admin/article_update.php");} // Заменить данные глав. страницы ИМ.
elseif ($admin_d3 == "up"){include("components/article/admin/article_up.php");} // Поднять главный пункт архива статей
elseif ($admin_d3 == "down"){include("components/article/admin/article_down.php");} // Опустить главный пункт архива статей
elseif ($admin_d3 == "pub"){include("components/article/admin/article_pub.php");} // Показать главный пункт архива статей
elseif ($admin_d3 == "unpub"){include("components/article/admin/article_unpub.php");} // Скрыть главный пункт архива статей

// раздел архива статей
elseif ($admin_d3 == "section"){include("components/article/admin/section.php");} // Вывод содержимого раздела
elseif ($admin_d3 == "sectionadd"){include("components/article/admin/section_add.php");} // Добавить раздел - форма ввода
elseif ($admin_d3 == "sectioninsert"){include("components/article/admin/section_insert.php");} // Вставить новый раздел
elseif ($admin_d3 == "sectionedit"){include("components/article/admin/section_edit.php");} // Редактировать раздел - форма ред.
elseif ($admin_d3 == "sectionupdate"){include("components/article/admin/section_update.php");} // Заменить данные в разделе
elseif ($admin_d3 == "sectionup"){include("components/article/admin/section_up.php");} // Опустить раздел
elseif ($admin_d3 == "sectiondown"){include("components/article/admin/section_down.php");} // Опустить раздел
elseif ($admin_d3 == "sectionpub"){include("components/article/admin/section_pub.php");} // Опубликовать раздел
elseif ($admin_d3 == "sectionunpub"){include("components/article/admin/section_unpub.php");} // Скрыть раздел
elseif ($admin_d3 == "sectiondelete"){include("components/article/admin/section_delete.php");} // Удалить раздел

// страница архива статей
elseif ($admin_d3 == "itemadd"){include("components/article/admin/item_add.php");} // Добавить статью - форма ввода
elseif ($admin_d3 == "iteminsert"){include("components/article/admin/item_insert.php");} // Вставить новую статью
elseif ($admin_d3 == "itemedit"){include("components/article/admin/item_edit.php");} // Редактировать статью - форма ред.
elseif ($admin_d3 == "itemupdate"){include("components/article/admin/item_update.php");} // Заменить данные в странице
elseif ($admin_d3 == "itemup"){include("components/article/admin/item_up.php");} // Поднять статью
elseif ($admin_d3 == "itemdown"){include("components/article/admin/item_down.php");} // Опустить статью
elseif ($admin_d3 == "itempub"){include("components/article/admin/item_pub.php");} // Опубликовать статью
elseif ($admin_d3 == "itemunpub"){include("components/article/admin/item_unpub.php");} // Скрыть статью
elseif ($admin_d3 == "itemdelete"){include("components/article/admin/item_delete.php");} // Удалить статью

else {include("components/article/admin/help.php");} // Вывести всё содержимое

?>