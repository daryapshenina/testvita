<?php
// DAN 2012
// Модуль авторизации
defined('AUTH') or die('Restricted access');

$modules_title_newusers = $modules_title;
$modules_titlepub_newusers = $modules_titlepub;
$suf_newusers = $modules_module_csssuf;

$quantity = $modules_p1;


if ($modules_pub == "1")
{	
	// верх модуля
	echo'<div class="mod-main'.$suf_newusers.'">
	<div class="mod-top'.$suf_newusers.'">';
	
	// Заголовок модуля
	if ($modules_titlepub_newusers == "1")
	{		
		echo '<div class="mod-title'.$suf_newusers.'">'.$modules_title_newusers.'</div>';
	}
	
	echo'</div>';
	
	// участники
	$query_profile = mysql_query("SELECT * FROM `sns_users_profile` WHERE `family` <> '' AND `name` <> '' ") or die ("Невозможно сделать выборку из таблицы - 1");	
	
	// количество участников
	$count = mysql_num_rows($query_profile);		

	// средняя часть
	echo'
		<div class="mod-mid'.$suf_newusers.'">
			<div class="mod-padding'.$suf_newusers.'">
				<div><a href="http://'.$site.'/s/profile/all">Нас уже: <b><font size="5">'.$count.'</b></font> человек</a></div>
				<div style="padding-top:5px;">
				';
				
	$query_profile = mysql_query("SELECT * FROM `sns_users_profile` WHERE `family` <> '' AND `name` <> '' ORDER BY `id` DESC LIMIT 0, $quantity") or die ("Невозможно сделать выборку из таблицы - 1");
	
	while($n = mysql_fetch_array($query_profile)):
		$psw_id = $n['psw_id'];	
		$name = $n['name']; 
		$family = $n['family'];
		$sex = $n['sex'];	
		$directory = $n['directory'];
		$photo = $n['photo'];

	
	if(!isset($photo) || $photo == '')
	{
		if ($sex == 1)
		{
			$photo_out = '<a target="_blank" href="http://'.$site.'/s/profile/'.$psw_id.'"><img src="http://'.$site.'/sns/profile/frontend/tmp/images/man.png" border="0" alt="'.$name.' '.$family.'"></a>';
		}
		elseif ($sex == 2)
		{
			$photo_out = '<a target="_blank" href="http://'.$site.'/s/profile/'.$psw_id.'"><img src="http://'.$site.'/sns/profile/frontend/tmp/images/woman.png" border="0" alt="'.$name.' '.$family.'"></a>';			
		}
		else
		{
			$photo_out = '<a target="_blank" href="http://'.$site.'/s/profile/'.$psw_id.'"><img src="http://'.$site.'/sns/profile/frontend/tmp/images/nophoto.png" border="0" alt="'.$name.' '.$family.'"></a>';			
		}		
	}
	else
	{		
		$photo_out = '<a target="_blank" href="http://'.$site.'/s/profile/'.$psw_id.'"><img src="http://'.$site.'/sns/photo/users/'.$directory.'/my_photo_preview.jpg" border="0" alt="'.$name.' '.$family.'"></a>';
	}
			echo'<div style="padding:5px; float:left">';
				echo'<div>'.$photo_out.'</div>';
				echo'<div style="height:30px; width:120px;"><a target="_blank" href="http://'.$site.'/s/profile/'.$psw_id.'">'.$name.' '.$family.'</a></div>';
			echo'</div>';
	
	endwhile;
		
		   echo'
		   		</div>
				<br class="clearfloat"/>
			</div>
		</div>            
		<div class="mod-bot'.$suf_newusers.'"></div></div>
	';					
}

?>