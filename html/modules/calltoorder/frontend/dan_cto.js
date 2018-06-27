
function f_calltoorder(_pi)
{
	var personal_information = '';

	if(_pi)
	{
		var personal_information = '<div class="mod_calltoorder_p_i"><input required checked title="Вы должны дать согласие перед отправкой" type="checkbox">Я согласен на <a href="/personal-information" target="_blank">обработку персональных данных</a></div><br />';
	}

	var content = '<form id="mod_calltoorder_form" method="post" action="/modules/calltoorder/frontend/mail.php"><div id="calltoorder_title">Заказать обратный звонок</div><br/><br/><div><div class="mod_calltoorder_text">Ваше имя <span style="color:#FF0000;">*</span></div><input class="input" type="text" value="" id="calltoorder_include1" name="cto_name" required><br /><br /><div class="mod_calltoorder_text">Телефон <span style="color:#FF0000;">*</span></div><input class="input" type="text" value="" id="calltoorder_include2" name="cto_phone" required="" pattern=".{17,}"><br /><br /></div>' + personal_information + '<br /><div class="calltoorder_submit"><input class="input" type="submit" value="Заказать" name="button" id="calltoorder_but"></div>';

	DAN.modal.add(content, 430);

	var input = document.getElementById("calltoorder_include2");
	VMasker(input).maskPattern("9 (999) 999-99-99");
}