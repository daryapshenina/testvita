<?php
defined('AUTH') or die('Restricted access');

echo '
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_module_authorization";
		var contextmenu_module_authorization = [			
			["admin/modules/up", "contextmenu_up", "Вверх"],
			["admin/modules/down", "contextmenu_down", "Вниз"],			
			["admin/modules/pub_no", "contextmenu_unpub", "Скрыть"],
			["admin/modules/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_module_authorization);
	});
</script>
';

?>
