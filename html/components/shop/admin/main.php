<?php
// определяет действие внутри компонента
defined('AUTH') or die('Restricted access');

include_once($root.'/components/shop/classes/classShopSettings.php');
$s = new classShopSettings;
$shopSettings = unserialize($s->settings);

// Главный пункт
if ($d[3] == "all"){include("components/shop/admin/shop_edit.php");} // На гланую страницу ИМ
elseif ($d[3] == "shopedit"){include("components/shop/admin/shop_edit.php");} // Редактировать главную страницу ИМ.
elseif ($d[3] == "excel"){include("components/shop/admin/excel/main.php");} // импорт и экспорт данных.
elseif ($d[3] == "shopupdate"){include("components/shop/admin/shop_update.php");} // Заменить данные глав. страницы ИМ.
elseif ($d[3] == "shopup"){include("components/shop/admin/shop_up.php");} // Поднять раздел
elseif ($d[3] == "shopdown"){include("components/shop/admin/shop_down.php");} // Опустить раздел
elseif ($d[3] == "shoppub"){include("components/shop/admin/shop_pub.php");} // Показать раздел
elseif ($d[3] == "shopunpub"){include("components/shop/admin/shop_unpub.php");} // Скрыть раздел

// Раздел
elseif ($d[3] == 'section')
{
	if ($d[4] == 'add'){include("components/shop/admin/section/add.php");} // Добавить раздел - форма ввода
	elseif ($d[4] == 'insert'){include("components/shop/admin/section/insert.php");} // Вставить новый раздел
	elseif ($d[4] == 'edit'){include("components/shop/admin/section/edit.php");} // Редактировать раздел - форма ред.
	elseif ($d[4] == 'update'){include("components/shop/admin/section/update.php");} // Заменить данные в разделе
	elseif ($d[4] == 'up'){include("components/shop/admin/section/up.php");} // Опустить раздел
	elseif ($d[4] == 'down'){include("components/shop/admin/section/down.php");} // Опустить раздел
	elseif ($d[4] == 'pub'){include("components/shop/admin/section/pub.php");} // Опубликовать раздел
	elseif ($d[4] == 'unpub'){include("components/shop/admin/section/unpub.php");} // Скрыть раздел
	elseif ($d[4] == 'delete'){include("components/shop/admin/section/delete.php");} // Удалить раздел
	elseif ($d[4] == 'delete_filter_ajax'){include("components/shop/admin/section/delete_filter_ajax.php");} // Удалить фильтр на аяксе
	else {include("components/shop/admin/section/section.php");} // Вывод содержимого раздела
}

// Товар
elseif ($d[3] == 'item')
{
	if ($d[4] == 'add'){include("components/shop/admin/item/add.php");} // Добавить страницу - форма ввода
	elseif ($d[4] == 'insert'){include("components/shop/admin/item/insert.php");} // Вставить новый товар
	elseif ($d[4] == 'copy'){include("components/shop/admin/item/copy.php");} // Копировать товар - форма ред.
	elseif ($d[4] == 'edit'){include("components/shop/admin/item/edit.php");} // Редактировать товар - форма ред.
	elseif ($d[4] == 'update'){include("components/shop/admin/item/update.php");} // Заменить данные в странице
	elseif ($d[4] == 'up'){include("components/shop/admin/item/up.php");} // Поднять товар
	elseif ($d[4] == 'down'){include("components/shop/admin/item/down.php");} // Опустить товар
	elseif ($d[4] == 'pub'){include("components/shop/admin/item/pub.php");} // Опубликовать товар
	elseif ($d[4] == 'unpub'){include("components/shop/admin/item/unpub.php");} // Скрыть товар
	elseif ($d[4] == 'delete'){include("components/shop/admin/item/delete.php");} // Удалить товар
	elseif ($d[4] == 'delete_char_ajax'){include("components/shop/admin/item/delete_char_ajax.php");} // Удалить характеристику
	elseif ($d[4] == 'related_list'){include("components/shop/admin/item/related/list.php");} // Лист сопутствующих товаров
	elseif ($d[4] == 'related_item'){include("components/shop/admin/item/related/item_add.php");} // Добавить сопутствующий товар
	elseif ($d[4] == 'related_delete'){include("components/shop/admin/item/related/delete.php");} // Удалить сопутствующий товар
}

// Заказы
elseif ($d[3] == "orders")
{
	if ($d[4] == "view"){include("components/shop/admin/orders/view.php");} // Просмотр заказа.
	elseif ($d[4] == "status"){include("components/shop/admin/orders/status.php");} // Статус.
	elseif ($d[4] == "delete"){include("components/shop/admin/orders/delete.php");} // Удалить заказ.
	else {include("components/shop/admin/orders/orders.php");} // Заказы.
}

// Настройки
elseif ($d[3] == 'settings')
{
	if ($d[4] == 'update'){include("components/shop/admin/settings/update.php");} // Обновить настройки
	else {include("components/shop/admin/settings/edit.php");} // Редактировать настройки
}

// Стикеры
elseif ($d[3] == 'stickers')
{
	if ($d[4] == 'update'){include("components/shop/admin/stickers/update.php");}
	else {include("components/shop/admin/stickers/edit.php");}
}

// Тип цен
elseif ($d[3] == 'price_type')
{
	switch ($d[4]) 
	{
		case 'add': include("components/shop/admin/price_type/edit.php"); break;
		case 'edit': include("components/shop/admin/price_type/edit.php"); break;
		case 'insert': include("components/shop/admin/price_type/insert.php"); break;
		case 'update': include("components/shop/admin/price_type/update.php"); break;
		case 'delete': include("components/shop/admin/price_type/delete.php"); break;		
		default: include("components/shop/admin/price_type/all.php"); break;
	}
}

// Пользователи
elseif ($d[3] == 'users')
{
	switch ($d[4]) 
	{
		case 'info': include("components/shop/admin/users/info.php"); break;
		case 'price_edit': include("components/shop/admin/users/price_edit.php"); break;
		case 'price_update': include("components/shop/admin/users/price_update.php"); break;	
		default: include("components/shop/admin/users/all.php"); break;
	}
}

// Оплата
elseif ($d[3] == "payment")
{
	if ($d[4] == "update"){include("components/shop/admin/payment/update.php");} // Обновить настройки
	else {include("components/shop/admin/payment/edit.php");} // Редактировать настройки
}

// Договор
elseif ($d[3] == "contract")
{
	if ($d[4] == "update"){include("components/shop/admin/contract/update.php");}
	else {include("components/shop/admin/contract/edit.php");}
}

// 1C
elseif ($d[3] == "1c")
{
	if ($d[4] == "update"){include("components/shop/admin/1c/update.php");}
	elseif ($d[4] == "import"){include("components/shop/admin/1c/import.php");}
	else {include("components/shop/admin/1c/edit.php");}
}

// YML
elseif ($d[3] == "yml")
	include_once "components/shop/admin/yml/main.php";

// CSV
elseif($d[3] == "csv_spec")
{
	switch($d[4])
	{
		case 'chars':
			include("components/shop/admin/csv_spec/ajax/chars.php");
			break;

		case 'images':
			include("components/shop/admin/csv_spec/ajax/images.php");
			break;

		case 'items':
			include("components/shop/admin/csv_spec/ajax/items.php");
			break;

		case 'clear_chars':
			include("components/shop/admin/csv_spec/ajax/clearChars.php");
			break;

		case 'clear':
			include("components/shop/admin/csv_spec/ajax/clear.php");
			break;

		default:
			include("components/shop/admin/csv_spec/main.php");
	}
}

// Характеристики
elseif ($d[3] == 'chars')
{
	if ($d[4] == 'add'){include("components/shop/admin/chars/add.php");}
	elseif ($d[4] == 'insert'){include("components/shop/admin/chars/insert.php");}
	elseif ($d[4] == 'edit'){include("components/shop/admin/chars/edit.php");}
	elseif ($d[4] == 'update'){include("components/shop/admin/chars/update.php");}
	elseif ($d[4] == 'ordering_ajax'){include("components/shop/admin/chars/ordering_ajax.php");}
	elseif ($d[4] == 'delete_ajax'){include("components/shop/admin/chars/delete_ajax.php");}
	elseif ($d[4] == 'delete_char_ajax'){include("components/shop/admin/chars/delete_char_ajax.php");}
	else {include("components/shop/admin/chars/mainpage.php");}
}

// Загрузка изображений
elseif ($d[3] == "img_upload_ajax"){include("components/shop/admin/img_upload_ajax.php"); exit;}
elseif ($d[3] == "img_delete_ajax"){include("components/shop/admin/img_delete_ajax.php"); exit;}

// Frontend Update
elseif ($d[3] == 'frontend_update')
{
	include("components/shop/admin/frontend_edit/update.php");
}

else {include("components/shop/admin/mainpage/mainpage.php");} // Вывести всё содержимое

?>