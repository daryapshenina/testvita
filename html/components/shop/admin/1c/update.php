<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/shop"); exit;}

$psw = $_POST['1c_psw'];
if(isset($_POST['1c_db_reset'])){$reset = $_POST['1c_db_reset'];} else{$reset = '';}

// если слишком мало символов в имени
if (strlen($psw) < 6) 
{
	die ('<div><font color="#FF0000">Количество символов в поле <b>Пароль</b> не должно быть меньше шести</font></div>');	
}

// проверяем на символы
if (!preg_match("/^[a-z0-9]{6,20}$/is", $psw))
{
	die ('<div><font color="#FF0000">Недопустимые символы в поле <b>Пароль</b>. Использовать только английские буквы и цифры от 6 до 20 символов</font></div>');
}

$shopSettings->c1_psw = $psw;	
$shopSettings->c1_db_reset = $reset;

$s_serialize = serialize($shopSettings);

$stmt = $db->prepare("UPDATE com_shop_settings SET parametr = :s_serialize WHERE name = 'settings'");
$stmt->execute(array('s_serialize' => $s_serialize));

	
Header ("Location: /admin/com/shop"); exit;
?>