<?php
defined('AUTH') or die('Restricted access');

include ($root.'/lib/image_processor.php');

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

$ph = $_FILES['photo']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.



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
	// Находим максимальное значение поля ordering
	if ($ordering == 0)
	{
		$stmt_item = $db->prepare('SELECT max(ordering) ordering FROM com_photo_items WHERE section = :section');
		$stmt_item->execute(array('section' => $section));
		$row_item = $stmt_item->fetch();

		$ordering = $row_item['ordering'] + 1;
	}

	$img_dr = $root.'/files/photo/';
	$img_dir = $root.'/files/photo/'.$section.'/';
	
	$time_ms = microtime('get_as_float');
	
	$ms = intval(1000*($time_ms - intval($time_ms)));

	$img_name = date('Y_m_d_H-i-s_').$ms;

	if(!is_dir($img_dr)){mkdir($img_dr, 0755);}
	if(!is_dir($img_dir)){mkdir($img_dir, 0755);}

	$tmp_path = $root.'/temp/temp_file.tmp';
	
	if(file_exists($ph))
	{
		Copy($ph,$tmp_path);

		switch($photo_settings['resize_method'])
		{
			case "1": // Умный ресайз
			{
				include_once($root."/classes/ImageResize/ImageResizeSmart.php");
				$img_small = new ImageResizeSmart($tmp_path, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			} break;

			case "2": // Подрезка
			{
				include_once($root."/classes/ImageResize/ImageResizeCutting.php");
				$img_small = new ImageResizeCutting($tmp_path, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			} break;

			case "3": // Скукожить
			{
				include_once($root."/classes/ImageResize/ImageResizeCompression.php");
				$img_small = new ImageResizeCompression($tmp_path, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			} break;

			default: // Указанный размер
			{
				include_once($root."/classes/ImageResize/ImageResize.php");
				$img_small = new ImageResize($tmp_path, $img_dir.$img_name.'.jpg', $photo_settings['x_small'], $photo_settings['y_small']);
			}
		}

		$img_small->run();
		
		$size = getimagesize($tmp_path); 
		$src_width = $size[0];
		$src_height = $size[1];

		if($src_width > $photo_settings['x_big']){$src_width = $photo_settings['x_big'];}
		if($src_height > $photo_settings['y_big']){$src_height = $photo_settings['y_big'];}
		
		include_once($root."/classes/ImageResize/ImageResize.php");
		$img_big = new ImageResize($tmp_path, $img_dir.$img_name.'_.jpg', $src_width, $src_height);

		$img_big->run();
	} // file_exists($ph)
	else {$img_small = ''; $img_big = '';}

	// === Загрузка фото ==============================================================================

	$stmt_item_insert = $db->prepare("
	INSERT INTO com_photo_items SET
	section = :section,
	name = :name,
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
	");

	$stmt_item_insert->execute(array(
	'section' => $section,
	'name' => $img_name,
	'title' => $title,
	'text' => $text,
	'link' => $link,
	'pub' => $pub,
	'ordering' => $ordering,
	'date' => date("Y-m-d H:i:s"),
	'tag_title' => $tag_title,
	'tag_description' => $tag_description
	));

	$item_id = $db->lastInsertId();

	if($bt_save == 'Сохранить'){Header ("Location: /admin/com/photo/section/".$section); exit;}
	else {Header ("Location: /admin/com/photo/item/edit/".$item_id); exit;}
}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции
?>
