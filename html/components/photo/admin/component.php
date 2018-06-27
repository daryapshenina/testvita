<?php
defined('AUTH') or die('Restricted access');
// выводит контекстное меню в административной панели для компонента "photo" (интернет-магазин)

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_photomain";
		var contextmenu_photomain = [
			["admin/com/photo", "contextmenu_tools", "Настройки"],
			["admin/com/photo/photoedit", "contextmenu_edit", "Редактировать"],
			["admin/com/photo/section/add", "contextmenu_add", "Добавить раздел"],
			["admin/com/photo/item/add", "contextmenu_add", "Добавить изображение"],
			["admin/com/photo/photoup", "contextmenu_up", "Вверх"],
			["admin/com/photo/photodown", "contextmenu_down", "Вниз"],
			["admin/com/photo/photopub", "contextmenu_pub", "Опубликовать"],
			["admin/com/photo/photounpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_photomain);


		class_name = "contextmenu_photo";
		var contextmenu_photo = [
			["admin/com/photo/section/add", "contextmenu_add", "Добавить раздел"],
			["admin/com/photo/item/add", "contextmenu_add", "Добавить изображение"],
			["admin/com/photo/section/edit", "contextmenu_edit", "Редактировать раздел"],
			["admin/com/photo/section/up", "contextmenu_up", "Вверх"],
			["admin/com/photo/section/down", "contextmenu_down", "Вниз"],
			["admin/com/photo/section/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/photo/section/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/photo/section/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_photo);
	});

</script>
');
?>