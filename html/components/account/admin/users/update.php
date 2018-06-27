<?php
defined('AUTH') or die('Restricted access');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/account/admin/users/image_resize.php');

if(isset($_POST["cancel"]))
{
	Header ("Location: /admin/com/account/users/all"); exit;
}

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

if($birth_date == '') $birth_date = '0000-00-00';

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

		$stmt_select = $db->prepare("SELECT email FROM com_account_users WHERE id = :id");
		$stmt_select->execute(array('id' => $d[5]));
		$email = $stmt_select->fetchColumn();
		
		$psw_hash = hash("sha256", 'DAN_psw'.$email.$psw); // генерируем хеш пароля
		$stmt_update = $db->prepare("UPDATE com_account_users SET psw = '".$psw_hash."', date_visit = NOW() WHERE id = :id");
		$stmt_update->execute(array('id' => $d[5]));
	}

	$stmt_select = $db->prepare("SELECT id FROM com_account_profile WHERE user_id = :user_id LIMIT 1");
	$stmt_select->execute(array('user_id' => $d[5]));
	
	// Обработка изображения
	$image = account_image_resize($d[5]);
	
	if($stmt_select->rowCount() == 0)
	{
		$stmt_profile_insert = $db->prepare("
			INSERT INTO com_account_profile SET
			user_id = :user_id,
			name = :name,
			surname = :surname,
			birth_date = :birth_date,
			company = :company,
			phone = :phone,
			image = :image,
			about = :about,
			country = :country,
			city = :city,
			address = :address,
			options = ''
		");

		$stmt_profile_insert->execute(array(
			'name' => $name,
			'surname' => $surname,
			'birth_date' => $birth_date,
			'company' => $company,
			'phone' => $phone,
			'image' => $image,
			'about' => $about,
			'country' => $country,
			'city' => $city,
			'address' => $address,
			'user_id' => $d[5]
		));	
	}
	else
	{
		if($image == 1) $SQL = "image = '1',"; else $SQL = '';

		$stmt_update = $db->prepare("
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

		$stmt_update->execute(array(
			'name' => $name,
			'surname' => $surname,
			'birth_date' => $birth_date,
			'company' => $company,
			'phone' => $phone,
			'about' => $about,
			'country' => $country,
			'city' => $city,
			'address' => $address,
			'user_id' => $d[5]
		));		
	}

}

if($err == ''){Header("Location: /admin/com/account/users/all"); exit;}

function a_com()
{
	global $err;
	
	echo '<h1>Ошибка!</h1><div>'.$err.'</div>';
}


?>