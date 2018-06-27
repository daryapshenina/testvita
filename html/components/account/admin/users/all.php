<?php
defined('AUTH') or die('Restricted access');

$head->addFile('/components/account/admin/users/tmp/all.css');
$head->addFile('/lib/css/font-awesome/css/font-awesome.min.css');
$head->addFile('/lib/contextmenu/contextmenu.css');
$head->addFile('/lib/contextmenu/contextmenu.js');

$head->addCode('
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(){

		var cm = [
			["admin/com/account/users/edit", "contextmenu_edit", "Редактировать"],
			["admin/com/account/users/ban", "contextmenu_block", "Заблокировать"],			
			["admin/com/account/users/unban", "contextmenu_unblock", "Разблокировать"],		
			["admin/com/account/users/delete", "contextmenu_delete", "Удалить"]
		];
		CONTEXTMENU.add("account_contextmenu", cm);

		var com_account_thumbs = document.getElementsByClassName("account_thumbnail");

		for(i = 0; i < com_account_thumbs.length; i++){
			com_account_thumbs[i].onclick = function(){
				var id = this.getAttribute("data-id");
				var floor_dir = 1000 * Math.floor(id/1000); 
				var img = "<img style=\"width:100%;\" src=\"/files/account/" + floor_dir + "/" + id + "/photo.jpg\">"
				DAN.modal.add(img);
			}
		}	
	});
</script>
');



function a_com()
{
	global $root, $db, $domain;

	$stmt_users = $db->query("
		SELECT a.id, a.email, a.date_visit, a.date_reg, a.status, p.name, p.surname, p.image
		FROM com_account_users a
		LEFT JOIN com_account_profile p ON p.user_id = a.id
		ORDER BY a.id desc
		");
	
	$out = '';
	while($m = $stmt_users->fetch())
	{
		if($m['status'] == '0')
		{
			$class = 'class="inactive"';
			$status = '<i class="fa fa-minus-circle" aria-hidden="true"></i>';
		}
		elseif($m['status'] == '1')
		{
			$class = 'class="active"';
			$status = '<i class="fa fa-check" aria-hidden="true"></i>';
		}
		elseif($m['status'] == '13')
		{
			$class = 'class="ban"';
			$status = '<i class="fa fa-ban" aria-hidden="true"></i>';
		}
		else
		{
			$class = '';
			$status = '';
		}
		
		if($m['image'] == 1)
		{
			$floor_id = 1000 * floor($m['id']/1000);
			$path = '/files/account/'.$floor_id.'/'.$m['id'].'/thumbnail.jpg?'.rand();
			$thumbnail = '<img data-id="'.$m['id'].'" class="account_thumbnail" src="'.$path.'">';			
		}
		else
		{
			$thumbnail = '';
		}

		$out .= '
			<tr '.$class.' >
				<td class="account_contextmenu" data-id="'.$m['id'].'">'.$m['id'].'</td>
				<td class="account_contextmenu" data-id="'.$m['id'].'">'.$thumbnail.'</td>
				<td class="account_contextmenu" data-id="'.$m['id'].'"><a href="/admin/com/account/users/view/'.$m['id'].'">'.$m['name'].' '.$m['surname'].'</a></td>
				<td class="account_contextmenu" data-id="'.$m['id'].'"><a href="/admin/com/account/view/'.$m['id'].'">'.$m['email'].'</a></td>
				<td>'.$m['date_visit'].'</td>
				<td>'.$m['date_reg'].'</td>
				<td style="text-align:center;">'.$status.'</td>
			</tr>		
		';
	}
	

	echo'
	<h1 class="account_title">Пользователи:</h1>
	<div class="account_add"><a class="button_green" href="/admin/com/account/users/add"><i class="account_add_ico fa fa-user-plus" aria-hidden="true"></i>Добавить пользователя</a></div>
	<table class="account_table">
		<tr>
			<th style="width:50px">№</th>
			<th style="width:90px"></th>
			<th>ФИО</th>
			<th style="width:150px">Email</th>
			<th style="width:150px">Дата посещения</th>
			<th style="width:150px">Дата регистрации</th>
			<th style="width:50px;"><i class="fa fa-check" aria-hidden="true"></i></th>
		</tr>
		'.$out.'
	</table>
	';
}
?>