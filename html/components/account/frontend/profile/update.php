<?php
defined('AUTH') or die('Restricted access');
include_once($root."/classes/Auth.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/components/account/admin/users/image_resize.php');

if(isset($_POST["cancel"]))
{
	Header ("Location: /account"); exit;
}

$user_id = Auth::check();


$psw = trim(mb_strtolower($_POST['account_password']));
$name = trim(strip_tags($_POST['name']));
$surname = trim(strip_tags($_POST['surname']));
$birth_date = $_POST['birth_date'];
$company = trim(strip_tags($_POST['company']));
$phone = trim(strip_tags($_POST['phone']));
$about = trim(strip_tags($_POST['about']));
$country = $_POST['country'];
$city = trim(strip_tags($_POST['city']));
$address = trim(strip_tags($_POST['address']));

$err = '';

if($err == '')
{
	$execute_arr = array();
	if($psw == '')
	{		
		$sql_psw = '';
	}
	else
	{
		if (!preg_match("/^[a-z0-9]{6,20}$/i",$psw)){$err .= 'Не правильно заполнено поле "Пароль"<br>';}
		$date = date("Y-m-d H:i:s");
		$psw_hash = hash("sha256", 'DAN_psw'.$email.$psw); // генерируем хеш пароля
		$stmt_user = $db->prepare("UPDATE com_account_users SET psw = '".$psw_hash."', date_visit = NOW() WHERE id = :id");
		$stmt_user->execute(array('id' => $user_id));
	}

	// Обработка изображения
	$image = account_image_resize($user_id);
	if($image == 1) $SQL = "image = '1',"; else $SQL = '';

	$stmt_profile = $db->prepare("
		UPDATE com_account_profile SET
		name = :name,
		surname = :surname,
		birth_date = :birth_date,
		company = :company,
		phone = :phone,
		".$SQL."
		about = :about,
		country = :country,
		city = :city,
		address = :address
		WHERE user_id = :user_id 
		LIMIT 1
	");

	$stmt_profile->execute(array(
		'user_id' => $user_id,
		'name' => $name,
		'surname' => $surname,
		'birth_date' => $birth_date,
		'company' => $company,
		'phone' => $phone,
		'about' => $about,
		'country' => $country,
		'city' => $city,
		'address' => $address
	));
}

if($err == ''){Header("Location: /account"); exit;}

function a_com()
{
	global $err;
	
	echo '<h1>Ошибка!</h1><div>'.$err.'</div>';
}


?>