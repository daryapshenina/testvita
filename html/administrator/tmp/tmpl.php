<?php defined("AUTH") or die("Restricted access"); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="Система управления сайтом" />
	<meta name="description" content="Система управления сайтом 5za" />
	<title>Шаблон административной панели сайта</title>
	<link rel="stylesheet" href="/administrator/tmp/admin_style.css" type="text/css"/>
	<link rel="stylesheet" href="/js/dan.framework.css" type="text/css"/>
	<link rel="stylesheet" href="/js/dan/dan.css" type="text/css"/>
	<link rel="stylesheet" href="/js/contextmenu/contextmenu.css" type="text/css"/>
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/dan.framework.js"></script>
	<script type="text/javascript" src="/js/dan/dan.js"></script>
	<script type="text/javascript" src="/js/contextmenu/contextmenu.js"></script>
	<script type="text/javascript" src="/administrator/plugins/ckeditor/ckeditor.js"></script>


	<script type="text/javascript">
    $(document).ready(function()
    {
        $("div.expand").show();
        $("#leftaccordion div.left_head").click(function()
        {
            $(this).next("div.left_body").slideToggle(300).siblings("div.left_body").slideUp("slow");
        });
    });
    </script>

	<? $head->out(); ?>

</head>

<body>

<table class="main_tab	minwidth1000">
	<tr>
		<td id="top">
		<table class="admin_table_main">
			<tr>
				<th nowrap id="top_1"><a  class="homepage" href="http://<? echo $site; ?>/admin/">Панель управления сайтом</a></th>
				<th nowrap id="top_2"><? echo "$site";?></th>
				<th nowrap id="top_3"><a class="site_view" target="_blank" href="http://<? echo $site; ?>/admin/viewsite">Просмотр</a></th>
				<th nowrap id="top_3"><a class="site_edit" target="_blank" href="http://<? echo $site; ?>/admin/wysiwyg">Визуально</a></th>
				<th nowrap id="top_4"><a class="logout" href="http://<? echo $site; ?>/admin/logout/">Выход</a></th>
				<th nowrap id="top_5"><? a_modules_upgrade(); ?></th>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
 	<tr>
		<td>
		<table class="main_tab">
			<tr>
				<td class="g-sep">&nbsp;</td>
				<td id="left">
					<div id="leftaccordion" class="left_list">
                        <div class="left_head"><div class="chat_title">Онлайн чат</div></div>
                        <div class="left_body"><? a_modules_chat(); ?></div>
                        <div class="left_head"><div class="com_title">Компоненты сайта</div></div>
                        <div class="left_body expand"><? a_modules_com(); ?></div>
                        <div class="left_head"><div class="mod_title">Модули сайта</div></div>
                        <div class="left_body"><? a_modules_mod(); ?></div>
						<div class="left_head"><a href="http://<? echo $site; ?>/administrator/plugins/browser/dan_browser.php?CKEditor=editor1&CKEditorFuncNum=2&langCode=ru" target="_blank" class="fm_title">Файловый менеджер</a></div>
                        <div class="left_head"><div class="tools_title">Настройки сайта</div></div>
                        <div class="left_body">
							<a id="settings" class="mod_left_link" href="http://<? echo $site; ?>/admin/settings/">Настройки</a>
							<a id="users" class="mod_left_link" href="http://<? echo $site; ?>/admin/users/">Администратор</a>
                        </div>

                        <div class="left_head"><a class="help_title" href="http://5za.ru/page/9">Помощь и обучение</a></div>

             		</div>
                </td>
				<td class="g-sep">&nbsp;</td>
				<td id="main"><? a_com(); ?></td>
				<td class="g-sep">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
 	<tr>
		<td>
        	<div class="v-sep">&nbsp;</div>
        	<div id="footer">Система управления сайтом 5za <a href="http://www.5za.ru/">www.5za.ru</a></div>
       </td>
	</tr>
</table>

</body>

</html>
