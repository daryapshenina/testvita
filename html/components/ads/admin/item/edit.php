<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/ads/admin/item/tmp/edit.css');
$head->addFile('/components/ads/admin/item/tmp/edit.js');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/css/imgareaselect-default.css');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.min.js');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.imgareaselect.pack.js');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.css');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.js');

function a_com()
{
	global $db, $d;
	
	$item_id = intval($d[5]);

	$stmt_section = $db->query("SELECT id, title FROM com_ads_section");
	$options = '';
	while($s = $stmt_section->fetch())
	{
		$options .= '<option value="'.$s['id'].'">'.$s['title'].'</option>';
	}

	$stmt_item = $db->prepare("SELECT user_id, title, content, image, date_c, pub FROM com_ads_item WHERE id = :id");
	$stmt_item->execute(array('id' => $item_id));
	$item = $stmt_item->fetch();

	$title = 'Редактировать объявление';
	$act = 'update';

	if($item['image'] == 1)
	{
		$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
		$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];
		$item['image_out'] = '<img id="ads_img" class="ads_image" alt="'.$item['title'].'" style="width:200px; height:150px;" src="'.$path.'/'.$item_id.'.jpg?'.rand().'">';
	}
	else $item['image_out'] = '<img id="ads_img" class="ads_image" alt="" style="width:200px; height:150px;" src="/components/ads/admin/item/tmp/nophoto.jpg">';

	
	echo '
	<h1>'.$title.'</h1>
	<form enctype="multipart/form-data" method="POST" action="/admin/com/ads/item/update/'.$item_id.'">
	<div class="ads_container">
		<div class="ads_section_container">
			<div class="ads_section_1">Раздел</div>
			<div class="ads_section_2">
				<select class="input" name="section">'.$options.'</select>
			</div>
		</div>
		<div class="ads_image">
			<div class="ads_img_container">'.$item['image_out'].'</div>
			<div class="ads_file_container">
				<input id="ads_file" onchange="img_files(this.files);" type="file" name="file">
				<input id="scale" type="hidden" name="scale" value="">
				<input id="x1" type="hidden" name="x1" value="">
				<input id="x2" type="hidden" name="x2" value="">
				<input id="y1" type="hidden" name="y1" value="">
				<input id="y2" type="hidden" name="y2" value="">
			</div>
		</div>
		<div class="ads_title_wrap"><input class="input ads_title" name="title" maxlength="70" placeholder="Заголовок объявления" value="'.$item['title'].'" required></div>
		<div class="ads_text_wrap"><textarea class="input ads_text" name="content" maxlength="1000" placeholder="Текст объявления" required>'.$item['content'].'</textarea></div>
		<div class="ads_title_wrap"><input class="input" name="date" type="datetime" value="'.$item['date_c'].'"></div>
	</div>
	<div class="ads_button_wrap">
	<br/>
	&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="bt_none">
	<br/>
	</div>
	</form>	
	';		
}
?>