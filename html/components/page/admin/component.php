<?php
defined('AUTH') or die('Restricted access');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_pagemain";
		var contextmenu_pagemain = [
			["admin/com/page/add", "contextmenu_add", "Добавить страницу"],
			["admin/com/page", "contextmenu_edit", "Редактировать"],			
			["admin/com/page/up", "contextmenu_up", "Вверх"],
			["admin/com/page/down", "contextmenu_down", "Вниз"],
			["admin/com/page/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/page/unpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_pagemain);
		
		
		class_name = "contextmenu_page";
		var contextmenu_page = [
			["admin/com/page/add", "contextmenu_add", "Добавить страницу"],
			["admin/com/page", "contextmenu_edit", "Редактировать"],			
			["admin/com/page/up", "contextmenu_up", "Вверх"],
			["admin/com/page/down", "contextmenu_down", "Вниз"],
			["admin/com/page/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/page/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/page/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_page);
	});

</script>
');
?>