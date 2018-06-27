<?php
defined('AUTH') or die('Restricted access');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_quotemain";
		var contextmenu_quotemain = [
			["admin/com/quote/quoteedit", "contextmenu_edit", "Редактировать"],
			["admin/com/quote/authors", "contextmenu_edit", "Авторы"],			
			["admin/com/quote/sectionadd", "contextmenu_add", "Добавить раздел"],
			["admin/com/quote/authoradd", "contextmenu_add", "Добавить автора"],
			["admin/com/quote/itemadd", "contextmenu_add", "Добавить цитату"],			
			["admin/com/quote/up", "contextmenu_up", "Вверх"],
			["admin/com/quote/down", "contextmenu_down", "Вниз"],
			["admin/com/quote/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/quote/unpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_quotemain);
		
		
		class_name = "contextmenu_quote";
		var contextmenu_quote = [
			["admin/com/quote/sectionadd", "contextmenu_edit", "Редактировать"],	
			["admin/com/quote/sectionadd", "contextmenu_add", "Добавить раздел"],
			["admin/com/quote/itemadd", "contextmenu_add", "Добавить цитату"],			
			["admin/com/quote/section/up", "contextmenu_up", "Вверх"],
			["admin/com/quote/section/down", "contextmenu_down", "Вниз"],
			["admin/com/quote/section/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/quote/section/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/quote/section/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_quote);
		
		
		class_name = "contextmenu_quoteauthors";
		var contextmenu_quoteauthors = [
			["admin/com/quote/authorsedit", "contextmenu_edit", "Редактировать"],	
			["admin/com/quote/authors", "contextmenu_add", "Все авторы"],
			["admin/com/quote/authoradd", "contextmenu_add", "Добавить автора"],			
			["admin/com/quote/section/authorsup", "contextmenu_up", "Вверх"],
			["admin/com/quote/section/authorsdown", "contextmenu_down", "Вниз"],
			["admin/com/quote/section/authorspub", "contextmenu_pub", "Опубликовать"],
			["admin/com/quote/section/authorsunpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_quoteauthors);		
	});

</script>
');

?>