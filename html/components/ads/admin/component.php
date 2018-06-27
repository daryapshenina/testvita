<?php
defined('AUTH') or die('Restricted access');
// Выводит контекстное меню в административной панели для компонента "ads" - объявления.

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_ads";
		var contextmenu_ads = [
			["admin/com/ads", "contextmenu_tools", "Настройки"],
			["admin/com/ads/section/add", "contextmenu_add", "Добавить раздел"],
			["admin/com/ads/item/add", "contextmenu_add", "Добавить объявление"],
			["admin/com/ads/section/edit", "contextmenu_edit", "Редактировать раздел"],
			/* ["admin/com/ads/section/up", "contextmenu_up", "Вверх"],
			["admin/com/ads/section/down", "contextmenu_down", "Вниз"],
			["admin/com/ads/section/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/ads/section/unpub", "contextmenu_unpub", "Скрыть"], */
			["admin/com/ads/section/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_ads);
	});

</script>
');
?>