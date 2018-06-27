<?php
// DAN 2011
// Настройки интернет магазина
defined('AUTH') or die('Restricted access');

$psw_1c = ($_POST["psw_1c"]);

// если слишком мало символов в имени
if (strlen($psw_1c) < 6) 
{
	die ('<div><font color="#FF0000">Количество символов в поле <b>Пароль</b> не должно быть меньше шести</font></div>');	
}

// проверяем на символы
if (!preg_match("/^[a-z0-9]{6,20}$/is",$psw_1c))
{
	die ('<div><font color="#FF0000">Недопустимые символы в поле <b>Пароль</b>. Использовать только английские буквы и цифры от 6 до 20 символов</font></div>');
}


// Условие - отменить
if ($none == "Отменить"){Header ("Location: http://".$site."/admin/com/shop/all"); exit;}
else {	
	
	// Обновляем данные в таблице "com_shop_settings"	
	$query_update_shop = "UPDATE `com_shop_settings` SET `parametr`='$psw_1c' WHERE `name`='1c_psw'";
	$sql_page = mysql_query($query_update_shop) or die ("Невозможно обновить данные 1");	

		
} // конец условия заполненного пункта меню
	
Header ("Location: http://".$site."/admin/com/shop/all"); exit;

?>