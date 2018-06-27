<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/administrator/chat/admin/chat_admin.js');

function a_com()
{ 
	echo '
		<h1>Онлайн чат:</h1>	
		<div id="chat_panel_upr" align="right"><div id="chat_delete_all_mess" class="chat_delete_all_mess_off" title="Нет выбранного пользователя">Удалить сообщения</div></div>
		<div id="chat_main">
			<div id="chat_list_user">
			<b>Список пользователей:</b>
			<div id="chat_list_user_li"></div>
			</div>
			<div id="chat_window"><div id="chat_window_padding"></div></div>
			
			<div id="chatlogs">
				<div id="chatlogs_right"></div>
				<div id="chatlogs_left"></div>
			</div>
			
			<div id="chat_otvet">
				<textarea id="chat_otvet_input"></textarea>
				<div id="chat_button_send">Отправить</div>
			</div>
		</div>
		
		<div style="display:none;" id="get_info"></div>
		<div style="display:none;" id="get_ip"></div>
	';
		
} // конец функции компонента
?>