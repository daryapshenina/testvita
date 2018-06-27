
function f_calltoorder(_pi)
{
	var personal_information = '';

	if(_pi)
	{
		var personal_information = '<div class="mod_calltoorder_p_i"><input required checked title="U moet akkoord gaan met de verzending" type="checkbox">Ik ga akkoord met <a href="/personal-information" target="_blank">verwerking van persoonsgegevens</a></div><br />';
	}

	var content = '<form id="mod_calltoorder_form" method="post" action="/modules/calltoorder/frontend/mail.php"><div id="calltoorder_title">Verzoek om een telefoontje terug</div><br/><br/><div><div class="mod_calltoorder_text">Jouw naam <span style="color:#FF0000;">*</span></div><input class="input" type="text" value="" id="calltoorder_include1" name="cto_name" required><br /><br /><div class="mod_calltoorder_text">Telefoonnummer <span style="color:#FF0000;">*</span></div><input class="input" type="text" value="" id="calltoorder_include2" name="cto_phone" required><br /><br /></div>' + personal_information + '<br /><div class="calltoorder_submit"><input class="input" type="submit" value="Bestel" name="button" id="calltoorder_but"></div>';

	DAN.modal.add(content, 430);

	var input = document.getElementById("calltoorder_include2");
	VMasker(input).maskPattern("9 (999) 999-99-99");
}