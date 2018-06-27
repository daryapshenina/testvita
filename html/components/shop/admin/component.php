<?php
defined('AUTH') or die('Restricted access');
// выводит контекстное меню в административной панели для компонента "shop" (интернет-магазин)

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_shopmain";
		var contextmenu_shopmain = [
			["admin/com/shop", "contextmenu_tools", "Настройки"],
			["admin/com/shop/shopedit", "contextmenu_edit", "Редактировать"],
			["admin/com/shop/orders", "contextmenu_orders", "Заказы"],
			["admin/com/shop/excel/export", "contextmenu_export", "Экспорт"],
			["admin/com/shop/excel/import", "contextmenu_import", "Импорт"],
			["admin/com/shop/section/add", "contextmenu_add", "Добавить раздел"],
			["admin/com/shop/item/add", "contextmenu_add", "Добавить товар"],
			["admin/com/shop/shopup", "contextmenu_up", "Вверх"],
			["admin/com/shop/shopdown", "contextmenu_down", "Вниз"],
			["admin/com/shop/shoppub", "contextmenu_pub", "Опубликовать"],
			["admin/com/shop/shopunpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_shopmain);


		class_name = "contextmenu_shop";
		var contextmenu_shop = [
			["admin/com/shop/section/add", "contextmenu_add", "Добавить раздел"],
			["admin/com/shop/item/add", "contextmenu_add", "Добавить товар"],
			["admin/com/shop/section/edit", "contextmenu_edit", "Редактировать раздел"],
			["admin/com/shop/section/up", "contextmenu_up", "Вверх"],
			["admin/com/shop/section/down", "contextmenu_down", "Вниз"],
			["admin/com/shop/section/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/shop/section/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/shop/section/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_shop);
	});

</script>
');
?>