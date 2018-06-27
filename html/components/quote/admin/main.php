<?php
// DAN 2012
// определяет действие внутри компонента

defined('AUTH') or die('Restricted access');
// для главного меню
// echo "--- $admin_d3 ---";

if ($admin_d3 == ""){include("components/quote/admin/help.php");} // Вывести всё содержимое

// главный пункт компонента "цитаты"
elseif ($admin_d3 == "quoteedit" || $admin_d3 == "all"){include("components/quote/admin/quote_edit.php");}
elseif ($admin_d3 == "quoteupdate"){include("components/quote/admin/quote_update.php");} 
elseif ($admin_d3 == "up"){include("components/quote/admin/quote_up.php");} 
elseif ($admin_d3 == "down"){include("components/quote/admin/quote_down.php");} 
elseif ($admin_d3 == "pub"){include("components/quote/admin/quote_pub.php");} 
elseif ($admin_d3 == "unpub"){include("components/quote/admin/quote_unpub.php");}

// цитаты
elseif ($admin_d3 == "itemadd"){include("components/quote/admin/item_add.php");} // Добавить цитату - форма ввода
elseif ($admin_d3 == "iteminsert"){include("components/quote/admin/item_insert.php");} // Вставить новую цитату
elseif ($admin_d3 == "itemedit"){include("components/quote/admin/item_edit.php");} // Редактировать цитату - форма ред.
elseif ($admin_d3 == "itemupdate"){include("components/quote/admin/item_update.php");} // Обновить цитату
elseif ($admin_d3 == "itemup"){include("components/quote/admin/item_up.php");} // Поднять цитату
elseif ($admin_d3 == "itemdown"){include("components/quote/admin/item_down.php");} // Опустить цитату
elseif ($admin_d3 == "itemdelete"){include("components/quote/admin/item_delete.php");} // Удалить цитату

// разделы
elseif ($admin_d3 == "section"){include("components/quote/admin/section.php");} // Вывод содержимого раздела
elseif ($admin_d3 == "sectionadd"){include("components/quote/admin/section_add.php");} // Добавить раздел - форма ввода
elseif ($admin_d3 == "sectioninsert"){include("components/quote/admin/section_insert.php");} // Вставить новый раздел
elseif ($admin_d3 == "sectionedit"){include("components/quote/admin/section_edit.php");} // Редактировать раздел - форма ред.
elseif ($admin_d3 == "sectionupdate"){include("components/quote/admin/section_update.php");} // Заменить данные в разделе
elseif ($admin_d3 == "sectionup"){include("components/quote/admin/section_up.php");} // Опустить раздел
elseif ($admin_d3 == "sectiondown"){include("components/quote/admin/section_down.php");} // Опустить раздел
elseif ($admin_d3 == "sectionpub"){include("components/quote/admin/section_pub.php");} // Опубликовать раздел
elseif ($admin_d3 == "sectionunpub"){include("components/quote/admin/section_unpub.php");} // Скрыть раздел
elseif ($admin_d3 == "sectiondelete"){include("components/quote/admin/section_delete.php");} // Удалить раздел

// авторы
elseif ($admin_d3 == "authors"){include("components/quote/admin/authors.php");} // Вывод всех авторов
elseif ($admin_d3 == "authoradd"){include("components/quote/admin/author_add.php");} // Добавить автора - форма ввода
elseif ($admin_d3 == "authorinsert"){include("components/quote/admin/author_insert.php");} // Вставить автора
elseif ($admin_d3 == "authoredit"){include("components/quote/admin/author_edit.php");} // Редактировать автора - форма ред.
elseif ($admin_d3 == "authorupdate"){include("components/quote/admin/author_update.php");} // Заменить данные автора
elseif ($admin_d3 == "authordelete"){include("components/quote/admin/author_delete.php");} // Удалить раздел

// все авторы
elseif ($admin_d3 == "authorsedit"){include("components/quote/admin/authors_all_edit.php");} // Редактировать "Все авторы"
elseif ($admin_d3 == "authorsup"){include("components/quote/admin/authors_all_up.php");} // Поднять пункт "Все авторы"
elseif ($admin_d3 == "authorsdown"){include("components/quote/admin/authors_all_down.php");} // Опустить пункт "Все авторы"
elseif ($admin_d3 == "authorspub"){include("components/quote/admin/authors_all_pub.php");} // Опубликовать пункт "Все авторы"
elseif ($admin_d3 == "authorsunpub"){include("components/quote/admin/authors_all_unpub.php");} // Скрыть пункт "Все авторы" 


else {include("components/quote/admin/help.php");} // Вывести всё содержимое

?>