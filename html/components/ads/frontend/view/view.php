<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/ads/frontend/my/tmp/my.css');


function component()
{
	global $root, $db;

	$stmt = $db->prepare("
		SELECT id, user_id, section, title, content, image, pub, date_c
		FROM com_ads_item
		WHERE user_id =:user_id
	");

	$stmt->execute(array('user_id' => Auth::check()));



	$out = '';
	$i = 0;
	$image = '';
	while($item = $stmt->fetch())
	{
		$i++;
		$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
		$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];			
		if($item['image'] == 1) $image = '<img alt="'.$item['title'].'" style="width:50px;" src="'.$path.'/'.$item['id'].'.jpg">';

		$out .= '<tr><td>'.$image.'</td><td><a href="/ads/view/'.$item['id'].'">'.$item['title'].'</a></td><td>'.$item['date_c'].'</td></tr>';
	}
	
	echo'
	<h1>Мои объявления</h1>
	<table class="ads_my_tab">
		'.$out.'
	</table>
	';		
}


?>