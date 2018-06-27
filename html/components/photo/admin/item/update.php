<?php
defined('AUTH') or die('Restricted access');

include ($root.'/lib/image_processor.php');

$id = intval($_POST["id"]);
$title = trim(htmlspecialchars($_POST["title"]));
$section = intval($_POST["section"]);
$pub = intval($_POST["pub"]);
$ordering = intval($_POST["ordering"]);
$link = trim(htmlspecialchars($_POST["link"]));
$tag_title = trim(htmlspecialchars($_POST["tag_title"]));
$tag_description = trim(htmlspecialchars($_POST["tag_description"]));
//$sef = checkingeditor($_POST["sef"]);
$text = $_POST["editor1"];

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com/photo/section/".$section); exit;}

// увеличиваем время выполнения
set_time_limit(60);
// выставить лимит в 512Mb
ini_set('memory_limit', '512M');
// лимит памяти
$memory_limit = get_cfg_var('memory_limit');

$memory_limit = (real)$memory_limit;

$title = replace_quotes($title);

$ph_name = checkingeditor_2($_FILES['photo']['name']); // Оригинальное имя файла на компьютере клиента.
$file_size = checkingeditor_2($_FILES['photo']['size']); // Размер в байтах принятого файла.

$photo_tmp = $_FILES['photo']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.



if ($file_size >= 5000000) // Проверка размера файла
{
	die ('
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Файл слишком большой!</title>
	</head>
	<body>
	<h3 align="center">Файл слишком большой! Максимальный размер файла не более 5 мб</h3>
	</body>
	</html>
	');
}

if (!isset($pub) || $pub == ""){$pub = "0";} // Условие публикации

$err = '';

// условия заполнения полей
if ($section == 0){$err .= '<div id="main-top">Не указан раздел!</div>';}

if($err == '')
{
	if(file_exists($photo_tmp))
	{
		$img_dr = $root.'/files/photo/';
		if(!is_dir($img_dr)){mkdir($img_dr, 0755);}
		
		$img_dir = $root.'/files/photo/'.$section.'/';
		if(!is_dir($img_dir)){mkdir($img_dir, 0755);}		

		
		// Удаляем старое изображение
		$stmt_item = $db->prepare("SELECT name FROM com_photo_items WHERE id = :id LIMIT 1");
		$stmt_item->execute(array('id' => $id));
		$img_old = $stmt_item->fetchColumn();
		
		// если есть файл изображения и его имя не пустое - удяляем файлы изображения
		if(is_file($img_dir.$img_old['name'].'.jpg')){unlink($img_dir.$img_old['name'].'.jpg');}
		if(is_file($img_dir.$img_old['name'].'_.jpg')){unlink($img_dir.$img_old['name'].'_.jpg');}

		
		$time_ms = microtime('get_as_float');
		$ms = intval(1000*($time_ms - intval($time_ms)));
		$img_name = date('Y_m_d_H-i-s_').$ms;


		switch($photo_settings['resize_method'])
		{
			case "1": // Умный ресайз
			{
				include_once($root."/classes/ImageResize/ImageResizeSmart.php");
				$img_small = new ImageResizeSmart($photo_tmp, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			} break;

			case "2": // Подрезка
			{
				include_once($root."/classes/ImageResize/ImageResizeCutting.php");
				$img_small = new ImageResizeCutting($photo_tmp, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			} break;

			case "3": // Скукожить
			{
				include_once($root."/classes/ImageResize/ImageResizeCompression.php");
				$img_small = new ImageResizeCompression($photo_tmp, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			} break;

			default: // Указанный размер
			{
				include_once($root."/classes/ImageResize/ImageResize.php");
				$img_small = new ImageResize($photo_tmp, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			}
		}

		$img_small->run();
		
		$size = getimagesize($photo_tmp); 
		$src_width = $size[0];
		$src_height = $size[1];

		if($src_width > $photo_settings['x_big']){$src_width = $photo_settings['x_big'];}
		if($src_height > $photo_settings['y_big']){$src_height = $photo_settings['y_big'];}
		
		include_once($root."/classes/ImageResize/ImageResize.php");
		$img_big = new ImageResize($photo_tmp, $img_dir.$img_name.'_.jpg', $src_width, $src_height);

		$img_big->run();
		
		

		$name_sql = 'name = :name,';
		$sql_arr = array(
		'section' => $section,
		'name' => $img_name,
		'title' => $title,
		'text' => $text,
		'link' => $link,
		'pub' => $pub,
		'ordering' => $ordering,
		'date' => date("Y-m-d H:i:s"),
		'tag_title' => $tag_title,
		'tag_description' => $tag_description,
		'id' => $id
		);
	} // file_exists($photo_tmp)
	else
	{
		// Находим старый раздел и если старый раздел != новому, то переносим фотографию в новый раздел
		$stmt_item = $db->prepare("SELECT section, name FROM com_photo_items WHERE id = :id LIMIT 1");
		$stmt_item->execute(array('id' => $id));
		$item = $stmt_item->fetch();
		
		if($section != $item['section'])
		{
			$old_dir = $root.'/files/photo/'.$item['section'].'/';		
			$new_dir = $root.'/files/photo/'.$section.'/';

			if(!is_dir($new_dir)){mkdir($new_dir, 0755);}

			copy($old_dir.$item['name'].'.jpg', $new_dir.$item['name'].'.jpg');
			copy($old_dir.$item['name'].'_.jpg', $new_dir.$item['name'].'_.jpg');
			
			unlink($old_dir.$item['name'].'.jpg');
			unlink($old_dir.$item['name'].'_.jpg');			
		}

		$name_sql = '';
		$sql_arr = array(
		'section' => $section,
		'title' => $title,
		'text' => $text,
		'link' => $link,
		'pub' => $pub,
		'ordering' => $ordering,
		'date' => date("Y-m-d H:i:s"),
		'tag_title' => $tag_title,
		'tag_description' => $tag_description,
		'id' => $id
		);		
	}


	// === Загрузка фото ==============================================================================

	$stmt_item_insert = $db->prepare("
	UPDATE com_photo_items SET
	section = :section,
	".$name_sql."
	title = :title,
	text = :text,
	link = :link,
	author = '0',
	pub = :pub,
	ordering = :ordering,
	new = '0',
	likes = '0',
	views = '0',
	date = :date,	
	tag_title = :tag_title,
	tag_description = :tag_description,
	comments_status = '0'
	WHERE id = :id
	");

	$stmt_item_insert->execute($sql_arr);

	$item_id = $db->lastInsertId();

	if($bt_save == 'Сохранить'){Header ("Location: /admin/com/photo/section/".$section); exit;}
	else {Header ("Location: /admin/com/photo/item/edit/".$id); exit;}
}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции
?>
