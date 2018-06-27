<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/ads/frontend/item/tmp/item.css');


function component()
{
	global $root, $db, $d;

	$ads_id = $d[2];

	$stmt = $db->prepare("
		SELECT i.id, i.user_id, i.section, i.title, i.content, i.image, i.pub, i.date_c, p.name, p.surname
		FROM com_ads_item i
		JOIN com_account_profile p
		ON p.user_id = i.user_id
		WHERE i.id = :id
	");

	$stmt->execute(array('id' =>$d[2]));

	$item = $stmt->fetch();

	$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
	$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];	

	if($item['image'] == 1) $image = '<img alt="'.$item['title'].'" src="'.$path.'/'.$item['id'].'_.jpg">';
		else $image = '<img alt="'.$item['title'].'" style="width:50px;" src="/components/ads/frontend/my/tmp/nophoto.jpg">';

	$text = '<div>'.$item['content'].'</div>';
	$text .= '<div class="ads_date">'.$item['date_c'].'<br>'.$item['name'].' '.$item['surname'].'</div>';

	echo'
	<h1>'.$item['title'].'</h1>
	<div class="flex_row ads_container">
		<div class="ads_img_container">'.$image.'</div>
		<div class="ads_text_container">'.$text.'</div>
	<div>
	';		
}


?>