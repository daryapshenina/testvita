function askQuestion(_theme)
{

	var askQuestion = document.getElementById('askQuestion');

	if(askQuestion !== null)
		return;

	askQuestion = document.createElement('div');
	askQuestion.id = 'askQuestion';

	document.body.appendChild(askQuestion);

	/**/

	askQuestionWindow = document.createElement('div');
	askQuestionWindow.id = 'askQuestionWindow';
	askQuestionWindow.innerHTML =
		'<div>'+
			'<form method="post" action="/components/shop/frontend/section/tmp/10/send.php">'+
				'<input type="hidden" name="theme" value="'+_theme+'">'+
				'<span>'+_theme+'</span>'+
				'<div><input name="email" type="text" class="input_1" placeholder="Ваш email" required></div>'+
				'<div><textarea name="question" class="input_1" placeholder="Ваш вопрос"></textarea></div>'+
				'<div>'+
					'<table class="table_0" style="width:100%;" >'+
						'<tr>'+
							'<td style="width:100px;"><img class="captcha_img" src="/administrator/captcha/pic.php" /></td>'+
							'<td>&nbsp;</td>'+
							'<td><input type="text" name="code" size="3" placeholder="Введите цифры" class="input_1" style="width:100%;" required pattern="[0-9]{4}" ></td>'+
						'</tr>'+
					'</table>'+
				'</div>'+
				'<div><input type="submit" value="Отправить"></div>'+
			'</form>'+
		'</div>';

	document.body.appendChild(askQuestionWindow);

	/**/

	askQuestion.addEventListener('click', function() {
		askQuestion.parentNode.removeChild(askQuestion);
		askQuestionWindow.parentNode.removeChild(askQuestionWindow);
	}.bind(this));

}
