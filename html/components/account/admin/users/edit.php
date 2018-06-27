<?php
defined('AUTH') or die('Restricted access');
include_once($root.'/lib/country.php');
$head->addFile('/lib/dan/DAN.js');
$head->addFile('/components/account/admin/users/tmp/edit.css');
$head->addFile('/components/account/admin/users/tmp/edit.js');
$head->addFile('/lib/css/font-awesome/css/font-awesome.min.css');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/css/imgareaselect-default.css');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.min.js');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.imgareaselect.pack.js');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.css');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.js');

$head->addCode('
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(){
		var thumbnail = document.getElementById("thumbnail");

		thumbnail.onclick = function(){
			var id = this.getAttribute("data-id");
			var floor_dir = 1000 * Math.floor(id/1000); 
			var img = "<img style=\"width:100%;\" src=\"/files/account/" + floor_dir + "/" + id + "/photo.jpg\">"
			DAN.modal.add(img);
		}
	});
</script>
');


function a_com()
{
	global $root, $db, $domain, $d, $mode, $country_arr;

	$stmt = $db->prepare("
		SELECT a.email, a.date_visit, a.date_reg, a.status, p.name, p.surname, p.birth_date, p.company, p.phone, p.image, p.about, p.country, p.city, p.address
		FROM com_account_users a
		LEFT JOIN com_account_profile p ON p.user_id = a.id
		WHERE a.id = :id
		LIMIT 1
	");
	
	$stmt->execute(array('id' => $d[5]));
	$account = $stmt->fetch();

	$floor_id = 1000 * floor($d[5]/1000);
	$path = '/files/account/'.$floor_id.'/'.$d[5];

	if($mode == 'add')
	{
		$title = 'Добавить пользователя';
		$act = 'insert';

		$email = '<input class="input" type="email" name="account_email" value="" placeholder="email" autocomplete="off" required autofocus>';
		$pass_req = 'pattern="[a-zA-Z0-9]{6,20}" required';
		$photo_out = '<img id="thumbnail" src="/components/account/admin/users/tmp/men.png">';

		$account['name'] = '';
		$account['surname'] = '';
		$birth_date = '';
		$account['company'] = '';
		$account['phone'] = '';
		$country_selected = '';
		$account['city'] = '';
		$account['address'] = '';
		$account['about'] = '';
	}

	if($mode == 'edit')
	{
		$title = 'Редактировать пользователя';
		$act = 'update/'.$d[5];

		$email = 'Email: <b>'.$account['email'].'</b>';
		$pass_req = '';

		$floor_id = 1000 * floor($d[5]/1000);
		
		if($account['image'] == 1) $photo_out = '<img id="thumbnail" data-id="'.$d[5].'" src="/files/account/'.$floor_id.'/'.$d[5].'/thumbnail.jpg?'.rand().'">';
		else $photo_out = '<img id="thumbnail" data-id="'.$d[5].'" src="/components/account/admin/users/tmp/men.png">';

		if($account['birth_date'] == '0000-00-00') $birth_date = ''; else $birth_date = $account['birth_date'];
		$country_selected = $account['country'];
	}


	$country = '<select class="input" name="country">';
	foreach($country_arr as $c)
	{
		if($c == $country_selected) $c_s = 'selected'; else  $c_s = '';
		$country .= '<option '.$c_s.'>'.$c.'<option>';
	}
	$country .= '</select>';

	echo'
	<h1 class="title">'.$title.'</h1>
	<form enctype="multipart/form-data" method="POST" action="/admin/com/account/users/'.$act.'">
	<div class="account_edit_container">
		<div class="account_edit_wrap">
			<div class="text_wrap">'.$email.'</div>
			<div class="input_wrap"><input id="account_password" class="input" type="password" name="account_password" value="" '.$pass_req.' title="Только английские буквы и цифры, не менее 6 символов" placeholder="Пароль" autocomplete="off"><i onclick="generatePass();" class="gen_pass fa fa-key" aria-hidden="true" title="Сгенерировать пароль"></i></div>
		</div>
	</div>
	<div class="account_edit_container">
		<div class="photo"><div id="image_container">'.$photo_out.'</div></div>
		<div class="fio">
			<div class="account_edit_wrap"><input class="input" type="text" name="name" placeholder="Имя" value="'.$account['name'].'"></div>
			<div class="account_edit_wrap"><input class="input" type="text" name="surname" placeholder="Фамилия" value="'.$account['surname'].'"></div>
			<div class="account_edit_wrap"><input class="input" type="date" name="birth_date"  min="1930-01-01" max="2002-01-01" value="'.$birth_date.'"></div>
			<div class="account_edit_wrap"><input class="input" type="text" name="company" placeholder="Компания" value="'.$account['company'].'"></div>
			<div class="account_edit_wrap"><input class="input" type="text" name="phone" placeholder="Телефон" value="'.$account['phone'].'"></div>
		</div>
	</div>
	<div class="account_edit_container">
		<input id="file" onchange="img_files(this.files);" type="file" name="file" value="">
		<input id="scale" type="hidden" name="scale" value="">
		<input id="x1" type="hidden" name="x1" value="">
		<input id="x2" type="hidden" name="x2" value="">
		<input id="y1" type="hidden" name="y1" value="">
		<input id="y2" type="hidden" name="y2" value="">		
	</div>
	<div class="account_edit_container">
		<div class="account_edit_wrap">
			<div class="text_address_wrap">'.$country.'</div>
		</div>
		<div class="account_edit_wrap">
			<div class="text_address_wrap"><input class="input" type="text" name="city" placeholder="Город" value="'.$account['city'].'"></div>
		</div>
		<div class="account_edit_wrap">
			<div class="text_address_wrap"><input class="account_edit_address input" type="text" name="address" placeholder="Адрес" value="'.$account['address'].'"></div>
		</div>		
		<div class="account_edit_wrap">&nbsp;</div>
		<div class="account_edit_wrap">
			<div class="text_address_wrap"><textarea class="input account_edit_textarea" name="about" placeholder="О пользователе">'.$account['about'].'</textarea></div>
		</div>
		<div class="account_edit_wrap">&nbsp;</div>
		<div class="account_edit_wrap">
			<div class="text_wrap"><input class="button_green" type="submit" name="submit" value="Сохранить"></div>
			<div class="input_wrap"><input class="button_gray" type="submit" name="cancel" value="Отменить"></div>
		</div>
	</div>
	</form>
	';
}
?>