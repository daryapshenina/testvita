<?php
defined('AUTH') or die('Restricted access');
include_once($root."/classes/Auth.php");
include_once($root."/classes/User.php");

$head->addFile('/lib/dan/DAN.js');
$head->addFile('/components/account/frontend/profile/tmp/view.css');

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
	global $domain, $err, $d;

	if(empty($d[2]))
	{
		$user_id = Auth::check();
		$title = 'Мой профиль';
		$edit = '<div class="account_wrap"><a class="account_edit_link" href="/account/edit">Редактировать</a></div>';
	}
	else
	{
		$user_id = $d[2];
		$title = 'Профиль пользователя';
		$edit = '';
	}

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

	if(!$u)
	{
		echo '<h1 class="title">Пользователь не найден</h1>';
	}
	else
	{
		$floor_id = 1000 * floor($user_id/1000);
		$path = '/files/account/'.$floor_id.'/'.$user_id.'/';

		if($u['birth_date'] == '0000-00-00') $birth_date = '<div class="account_wrap">Дата рождения: <b>'.$u['birth_date'].'</b></div>'; else $birth_date = '';

		echo'
		<h1 class="title">'.$title.':</h1>
		<div class="account_container">
			<div class="photo"><div id="image_container"><img data-id="'.$user_id.'" id="thumbnail" src="'.$path .'thumbnail.jpg?'.rand().'"></div></div>
			<div class="fio">
				<div class="account_wrap"><b>'.$u['name'].' '.$u['surname'].'</b></div>	
				<div class="account_wrap">Компания: <b>'.$u['company'].'</b></div>
				<div class="account_wrap">Тел: <b>'.$u['phone'].'</b></div>
				<div class="account_wrap">Email: <b>'.$u['email'].'</b></div>
				'.$edit.'
			</div>
		</div>
		<div class="account_container">
			<div class="account_wrap"><b>'.$u['country'].', '.$u['city'].', '.$u['address'].'</b></div>	
			<div class="account_wrap"><b>'.$u['about'].'</b></div>
		</div>
		';		
	}	
}

?>