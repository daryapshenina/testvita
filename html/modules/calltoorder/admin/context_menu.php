<?php
defined('AUTH') or die('Restricted access');

echo '
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_module_calltoorder";
		var contextmenu_module_calltoorder = [			
			["admin/modules/up", "contextmenu_up", "Вверх"],
			["admin/modules/down", "contextmenu_down", "Вниз"],
			["admin/modules/pub_all", "contextmenu_pub", "Показывать везде"],
			["admin/modules/pub_pc", "contextmenu_pub", "Только на компьютерах"],
			["admin/modules/pub_mobile", "contextmenu_pub", "Только на телефонах"],			
			["admin/modules/pub_no", "contextmenu_unpub", "Скрыть"],
			["admin/modules/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_module_calltoorder);
	});
</script>
';

?>
