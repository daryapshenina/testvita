<?php
// выводит контекстное меню в административной панели для компонента "form" (интернет-магазин) 
defined('AUTH') or die('Restricted access');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_formmain";
		var contextmenu_formmain = [
			["admin/com/form/edit", "contextmenu_edit", "Редактировать"],			
			["admin/com/form/up", "contextmenu_up", "Вверх"],
			["admin/com/form/down", "contextmenu_down", "Вниз"],
			["admin/com/form/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/form/unpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_formmain);
	});

</script>
');

?>