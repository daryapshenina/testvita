<?php
defined('AUTH') or die('Restricted access');
include_once($root."/classes/Auth.php");
include_once($root."/classes/User.php");
include_once($root.'/lib/country.php');

$head->addFile('/lib/dan/DAN.js');
$head->addFile('/components/account/frontend/profile/tmp/edit.css');
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


function component()
{
	global $root, $domain, $d, $country_arr;

	if(!Auth::check())
	{
		echo '<h1>Для редактирования профиля Вам необходимо <a href="/account">авторизироваться</a></h1>';
	}
	else
	{
		$user_id = Auth::check();
		$user = new User;

		$user->options['surname'] = true;
		$user->options['birth_date'] = true;
		$user->options['company'] = true;
		$user->options['phone'] = true;
		$user->options['image'] = true;
		$user->options['about'] = true;
		$user->options['country'] = true;
		$user->options['city'] = true;
		$user->options['address'] = true;
		$user->options['options'] = true;

		$u = $user->profile($user_id);

		$floor_id = 1000 * floor($user_id/1000);
		$path = '/files/account/'.$floor_id.'/'.$user_id.'/';

		if($u['birth_date'] == '0000-00-00') $birth_date = '<div class="account_wrap">Дата рождения: <b>'.$u['birth_date'].'</b></div>'; else $birth_date = '';

		$title = 'Редактировать пользователя';

		$email = 'Email: <b>'.$u['email'].'</b>';

		if(is_file($root.'/files/account/'.$floor_id.'/'.$user_id.'/thumbnail.jpg'))
		{
			$photo_out = '<img id="thumbnail" data-id="'.$user_id.'" src="/files/account/'.$floor_id.'/'.$user_id.'/thumbnail.jpg?'.rand().'">';			
		}
		else
		{
			$photo_out = '<img id="thumbnail" src="/components/account/admin/users/tmp/men.png">';			
		}

		if($u['birth_date'] == '0000-00-00') $birth_date = ''; else $birth_date = $u['birth_date'];
		$country_selected = $u['country'];

		$country = '<select class="input" name="country">';
		foreach($country_arr as $c)
		{
			if($c == $country_selected) $c_s = 'selected'; else  $c_s = '';
			$country .= '<option '.$c_s.'>'.$c.'<option>';
		}
		$country .= '</select>';

		echo'
		<h1 class="title">'.$title.'</h1>
		<form enctype="multipart/form-data" method="POST" action="/account/update">
		<div class="account_edit_container">
			<div class="account_edit_wrap">
				<div class="text_wrap">'.$email.'</div>
				<div class="input_wrap"><input id="account_password" class="input" type="password" name="account_password" value="" title="Только английские буквы и цифры, не менее 6 символов" placeholder="Пароль" autocomplete="off"><i onclick="generatePass();" class="gen_pass fa fa-key" aria-hidden="true" title="Сгенерировать пароль"></i></div>
			</div>
		</div>
		<div class="account_edit_container">
			<div class="photo"><div id="image_container">'.$photo_out.'</div></div>
			<div class="fio">
				<div class="account_edit_wrap"><input class="input" type="text" name="name" placeholder="Имя" value="'.$u['name'].'"></div>
				<div class="account_edit_wrap"><input class="input" type="text" name="surname" placeholder="Фамилия" value="'.$u['surname'].'"></div>
				<div class="account_edit_wrap"><input class="input" type="date" name="birth_date"  min="1930-01-01" max="2002-01-01" value="'.$birth_date.'"></div>
				<div class="account_edit_wrap"><input class="input" type="text" name="company" placeholder="Компания" value="'.$u['company'].'"></div>
				<div class="account_edit_wrap"><input class="input" type="text" name="phone" placeholder="Телефон" value="'.$u['phone'].'"></div>
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
				<div class="text_address_wrap"><input class="input" type="text" name="city" placeholder="Город" value="'.$u['city'].'"></div>
			</div>
			<div class="account_edit_wrap">
				<div class="text_address_wrap"><input class="account_edit_address input" type="text" name="address" placeholder="Адрес" value="'.$u['address'].'"></div>
			</div>		
			<div class="account_edit_wrap">&nbsp;</div>
			<div class="account_edit_wrap">
				<div class="text_address_wrap"><textarea class="input account_edit_textarea" name="about" placeholder="О пользователе">'.$u['about'].'</textarea></div>
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
}

?>