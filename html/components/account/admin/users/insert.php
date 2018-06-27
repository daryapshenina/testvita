<?php
defined('AUTH') or die('Restricted access');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/account/admin/users/image_resize.php');

if(isset($_POST["cancel"]))
{
	Header ("Location: /admin/com/account/users/all"); exit;
}
$email = trim(mb_strtolower($_POST['account_email']));
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
if (!preg_match("/^[^@]+@[^@]+\.[a-zа-я]{2,20}$/ui",$email)){$err .= 'Не правильно заполнено поле "Email"<br>';}
if (!preg_match("/^[a-z0-9]{6,20}$/i",$psw)){$err .= 'Не правильно заполнено поле "Пароль"<br>';}

if($err == '')
{
	// Проверяем существование email
	$stmt = $db->prepare("SELECT id FROM com_account_users WHERE email = :email LIMIT 1");
	$stmt->execute(array('email' => $email));

	if($stmt->rowCount() > 0){$err .= 'Email уже зарегистрирован';}
	else
	{
		$psw_hash = hash("sha256", 'DAN_psw'.$email.$psw); // генерируем хеш пароля

		$stmt_users_insert = $db->prepare('INSERT INTO com_account_users SET email = :email, psw = :psw, cid = :cid, date_reg = :date_reg, date_visit = :date_visit, status = :status');
		$stmt_users_insert->execute(array('email' => $email, 'psw' => $psw_hash, 'cid' => '', 'date_reg' => date("Y-m-d H:i:s"), 'date_visit' => date("Y-m-d H:i:s"), 'status' => '1'));

		$user_id = $db->lastInsertId();

		// Обработка изображения
		$image = account_image_resize($user_id);

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
			'user_id' => $user_id,
			'name' => $name,
			'surname' => $surname,
			'birth_date' => $birth_date,
			'company' => $company,
			'phone' => $phone,
			'image' => $image,
			'about' => $about,
			'country' => $country,
			'city' => $city,
			'address' => $address
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