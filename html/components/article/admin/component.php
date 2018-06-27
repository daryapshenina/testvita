<?php
// выводит контекстное меню в административной панели для компонента "article" (архив статей) 
defined('AUTH') or die('Restricted access');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_articlemain";
		var contextmenu_articlemain = [
			["admin/com/article/articleedit", "contextmenu_edit", "Редактировать"],			
			["admin/com/article/sectionadd", "contextmenu_add", "Добавить раздел"],
			["admin/com/article/itemadd", "contextmenu_add", "Добавить статью"],		
			["admin/com/article/up", "contextmenu_up", "Вверх"],
			["admin/com/article/down", "contextmenu_down", "Вниз"],
			["admin/com/article/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/article/unpub", "contextmenu_unpub", "Скрыть"]
		];
		contextmenu(class_name, contextmenu_articlemain);
		
		
		class_name = "contextmenu_article";
		var contextmenu_article = [			
			["admin/com/article/sectionadd", "contextmenu_add", "Добавить раздел"],
			["admin/com/article/itemadd", "contextmenu_add", "Добавить статью"],
			["admin/com/article/sectionedit", "contextmenu_add", "Редактировать раздел"],			
			["admin/com/article/sectionup", "contextmenu_up", "Вверх"],
			["admin/com/article/sectiondown", "contextmenu_down", "Вниз"],
			["admin/com/article/sectionpub", "contextmenu_pub", "Опубликовать"],
			["admin/com/article/sectionunpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/article/sectiondelete", "contextmenu_delete", "Удалить"]			
		];
		contextmenu(class_name, contextmenu_article);
	});

</script>
');
?>