<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/lib/dan/DAN.js');
$head->addFile('/components/account/admin/users/tmp/edit.css');

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
	global $root, $db, $domain, $d;

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

	if($account['birth_date'] == '0000-00-00') $birth_date = '<div class="account_edit_wrap">Дата рождения: <b>'.$account['birth_date'].'</b></div>'; else $birth_date = '';

	echo'
	<h1 class="title">Профиль пользователя:</h1>
	<div class="account_edit_container">
		<div class="photo"><div id="image_container"><img data-id="'.$d[5].'" id="thumbnail" src="'.$path .'/thumbnail.jpg?'.rand().'"></div></div>
		<div class="fio">
			<div class="account_edit_wrap"><b>'.$account['name'].' '.$account['surname'].'</b></div>
			
			<div class="account_edit_wrap">Компания: <b>'.$account['company'].'</b></div>
			<div class="account_edit_wrap">Тел: <b>'.$account['phone'].'</b></div>
			<div class="account_edit_wrap">Email: <b>'.$account['email'].'</b></div>
		</div>
	</div>
	<div class="account_edit_container">
		<div class="account_edit_wrap"><b>'.$account['country'].', '.$account['city'].', '.$account['address'].'</b></div>	
		<div class="account_edit_wrap"><b>'.$account['about'].'</b></div>
	</div>
	';
}
?>