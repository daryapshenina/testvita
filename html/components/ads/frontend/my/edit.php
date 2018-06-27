<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/ads/frontend/my/tmp/edit.css');
$head->addFile('/components/ads/frontend/my/tmp/edit.js');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/css/imgareaselect-default.css');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.min.js');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.imgareaselect.pack.js');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.css');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.js');

if(Auth::check())
{
	function component()
	{
		global $db, $d;

		$stmt_section = $db->query("SELECT id, title FROM com_ads_section");
		$options = '';
		while($s = $stmt_section->fetch())
		{
			$options .= '<option value="'.$s['id'].'">'.$s['title'].'</option>';
		}

		$item['image_out'] = '<img id="ads_img" class="ads_image" alt="" style="width:200px; height:150px;" src="/components/ads/frontend/my/tmp/nophoto.jpg">';
	
		if($d[2] == 'add')
		{
			$title = 'Добавить объявление';
			$act = 'insert';

			$item['title'] = '';
			$item['content'] = '';
		}
		else
		{
			$stmt_item = $db->prepare("SELECT title, content, image, pub FROM com_ads_item WHERE id = :id AND user_id = :user_id");
			$stmt_item->execute(array('id' => $d[3], 'user_id' => Auth::check()));
			$item = $stmt_item->fetch();

			$title = 'Редактировать объявление';
			$act = 'update';

			if($item['image'] == 1)
			{
				$floor_id = 1000 * floor(Auth::check()/1000); // тысячная папка
				$path = '/files/ads/'.$floor_id.'/'.Auth::check();
				$item['image_out'] = '<img id="ads_img" class="ads_image" alt="'.$item['title'].'" style="width:200px; height:150px;" src="'.$path.'/'.$d[3].'.jpg">';
			}		
		}
		
		echo '
		<h1>'.$title.'</h1>
		<form enctype="multipart/form-data" method="POST" action="/ads/my/'.$act.'/'.$d[3].'">
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
		</div>
		<div class="ads_button_wrap">
			<input class="button_green" type="submit" name="submit" value="Сохранить">
			<input class="button_gray" type="submit" name="cancel" value="Отменить"></div>
		</div>
		</form>	
		';		
	}
}
else
{
	Header("Location: /account/my");
}


?>