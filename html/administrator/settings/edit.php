<?php
// Настройки сайта
defined('AUTH') or die('Restricted access');

if(isset($_POST["none"])){$none = $_POST["none"];} else{$none = '';}// кнопка 'Отменить'

// Условие - отменить
if ($none == "Отменить"){Header ("Location: /admin/"); exit;}

// загрузка XML-файла тем.
$xmltemplate = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'/tmp/template.xml');

function a_com()
{
	global $db, $domain, $xmltemplate;

	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/tools.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Настройки сайта:</div>
		<div>&nbsp;</div>

		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v"></td>
				<td class="cell-title-modules" >Параметр</td>
				<td class="cell-desc-modules" >Значение</td>
			</tr>
		</table>
	';



	if(Settings::instance()->getValue('Сайт включен') == 1){$publish = '<input id="site_publish" class="input" type="checkbox" name="publish" value="1" checked /><label for="site_publish"></label>';}
	else {$publish = '<input id="site_publish" class="input" type="checkbox" name="publish" value="1" /><label for="site_publish"></label>';}

	if(Settings::instance()->getValue('personal_information') == 1){$pi = '<input id="personal_information" class="input" type="checkbox" name="personal_information" value="1" checked /><label for="personal_information"></label>';}
	else {$pi = '<input id="personal_information" class="input" type="checkbox" name="personal_information" value="1" /><label for="personal_information"></label>';}	

	// вывод параметров
		echo'
			<form enctype="multipart/form-data" method="POST" action="/admin/settings/update/">
			<table class="w100_bs1">
				<tr>
					<td class="cell-title-modules"><b>Сайт включён</b></td>
					<td class="cell-desc-modules">'.$publish.'</td>
				</tr>
				<tr>
					<td class="cell-title-modules"><b>E-mail</b></td>
					<td class="cell-desc-modules"><input type="text" name="email" size="30" value = "'.Settings::instance()->getValue('email').'"/></td>
				</tr>
				<tr>
					<td class="cell-title-modules"><b>Название сайта</b></td>
					<td class="cell-desc-modules"><input type="text" name="title" size="82" value = "'.Settings::instance()->getValue('Наименование сайта').'"/></td>
				</tr>
				<tr>
					<td class="cell-title-modules"><b>Описание сайта</b></td>
					<td class="cell-desc-modules"><textarea rows="3" name="description" cols="84">'.Settings::instance()->getValue('Описание сайта').'</textarea></td>
				</tr>
				<tr>
					<td class="cell-title-modules"><b>Персональные данные</b> (галочка в формах)</td>
					<td class="cell-desc-modules">'.$pi.'</td>
				</tr>
				<tr>
					<td class="cell-title-modules"><b>Код счётчика статистики</b></td>
					<td class="cell-desc-modules"><textarea rows="12" name="statistics" cols="84">'.Settings::instance()->getValue('statistics').'</textarea></td>
				</tr>
				<tr>
					<td class="cell-title-modules"><b>Метатег, подтверждающий права на сайт для Яндекса, Гугла</b></td>
					<td class="cell-desc-modules"><textarea rows="2" name="meta_tag" cols="84">'.Settings::instance()->getValue('meta_tag').'</textarea></td>
				</tr>				
				<tr>
					<td class="cell-title-modules"><b>Загрузить новую иконку сайта</b></td>
					<td class="cell-desc-modules"><input type="file" name="favicon" size="20"> Формат иконки должен быть: <b>favicon.ico</b></td>
				</tr>
			</table>
			<br/>
			&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
			<br/>
			&nbsp;
			</form>
		';



} // конец функции компонента
?>