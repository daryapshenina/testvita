<?php
defined('AUTH') or die('Restricted access');

// увеличиваем время выполнения
set_time_limit(60);
// выставить лимит в 512Mb
ini_set('memory_limit', '512M');
// лимит памяти
$memory_limit = get_cfg_var('memory_limit');
$memory_limit = (real)$memory_limit;

include ($root.'/lib/image_processor.php');

$action = $_POST['act'];
$id = intval($_POST['id']);
$tmp_file = $_POST['img_src'];

if($action = 'upload')
{
	$img_dir = $root.'/components/shop/photo/';
	$img_name = date('ymdHis').'.jpg';
	$img_big_name = str_replace('.jpg','_.jpg', $img_name);

	$img_arr = img_load ($shopSettings->small_resize_method, $img_name, $img_dir, $tmp_file, $shopSettings->x_small, $shopSettings->y_small, $shopSettings->x_big, $shopSettings->y_big);
	
	$stmt_photo = $db->prepare('SELECT photo, photo_more FROM com_shop_item WHERE id = :id LIMIT 1');
	$stmt_photo->execute(array('id' => $id));	
	$n = $stmt_photo->fetch();
	
	$photo = $n['photo'];
	$photo_more = $n['photo_more']; 	

	
	if ($photo == '') // если нет главной фотки - ставим новую главной
	{
		$photo_main_sql = "photo = '".$img_name."', photo_big = '".$img_big_name."',";
		$photo_more = '';
	}
	else
	{
		$photo_main_sql = '';	
		$photo_more .= $img_name.';';		
	}

	// Обновляем базу данных
	$stmt_update = $db->prepare('UPDATE com_shop_item SET '.$photo_main_sql.' photo_more = :photo_more WHERE id = :id LIMIT 1;');
	$stmt_update->execute(array('id' => $id, 'photo_more' => $photo_more));
		
	$json_data = array('img_small'=>$img_arr[0], 'img_big'=>$img_arr[1]);
	echo json_encode($json_data);
}
exit;

?>
