<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

$title = htmlspecialchars($_POST["title"]);
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
if(isset($_POST["block"])){$block = strip_tags($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
$margin_w = intval($_POST["margin_w"]);
$margin_h = intval($_POST["margin_h"]);
$photo_tmp = $_FILES['photo']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере.
$content = trim($_POST["content_1"]);
$content_2 = trim($_POST["content_2"]);
$bg_color = $_POST["bg_color"];
$link = trim($_POST["link"]);

// === Загрузка фото
if($photo_tmp)
{
	$stmt_mod = $db->prepare("SELECT p10 FROM modules WHERE module = 'photo_s' AND id =:id");
	$stmt_mod->execute(array('id' => $mod_id));
	
	$photo_name_old = $stmt_mod->fetchColumn();
	
	if(!empty($photo_name_old)) unlink($root.'/files/modules/photo_s/'.$photo_name_old);

	$photo_name = date("YmdHis").'.jpg';	
	
	if(!file_exists($root.'/files/modules/')){mkdir($root.'/files/modules/', 0755);}
	if(!file_exists($root.'/files/modules/photo_s/')){mkdir($root.'/files/modules/photo_s/', 0755);}

	include_once($root."/classes/ImageResize/ImageResizeCutting.php");
	$img_small = new ImageResizeCutting($photo_tmp, $root.'/files/modules/photo_s/'.$photo_name, 500, 500);
	$img_small->run();
	
	$photo_sql = "p10 = '".$photo_name."',";
}
else{$photo_sql = '';}

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	block = :block,
	margin_w = :margin_w,
	margin_h = :margin_h,
	bg_color = :bg_color,
	content = :content,
	content_2 = :content_2,
	p1 = :p1,
	".$photo_sql."
	ordering = :ordering
	WHERE id = :id AND module = 'photo_s' LIMIT 1
");

$stmt_update->execute(array(
	'title' => $title,
	'pub' => $pub,
	'block' => $block,
	'margin_w' => $margin_w,
	'margin_h' => $margin_h,
	'bg_color' => $bg_color,
	'content' => $content,
	'content_2' => $content_2,
	'p1' => $link,
	'ordering' => $ordering,
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/photo_s/".$mod_id); exit;}

?>