<?php
defined('AUTH') or die('Restricted access');

$id = $d[3];
if(isset($_POST["title"])){$title = htmlspecialchars($_POST["title"]);} else {$title = '';}
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else {$pub = 0;}
//if(isset($_POST["titlepub"])){$titlepub = intval($_POST["titlepub"]);} else{$titlepub = 0;}
if(isset($_POST["block"])){$block = htmlspecialchars($_POST["block"]);} else{$block = '';}
$effect = $_POST['effect'];	
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}
if(isset($_POST["width"])){$width = intval($_POST["width"]);} else{$width = 0;}
if(isset($_POST["height"])){$height = intval($_POST["height"]);} else{$height = 0;}
if(isset($_POST["color"])){$color = $_POST["color"];} else{$color = '#4a4a4a';}
if(isset($_POST["link"])){$link = $_POST["link"];} else{$link = '';}
if(isset($_POST["margin_w"])){$margin_w = $_POST["margin_w"];} else{$margin_w = 10;}	
if(isset($_POST["margin_h"])){$margin_h = $_POST["margin_h"];} else{$margin_h = 20;}
if(isset($_POST["editor1"])){$content = $_POST["editor1"];} else{$content = '';}
if(isset($_POST["editor2"])){$content_2 = $_POST["editor2"];} else{$content_2 = '';}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	content = :content,
	content_2 = :content_2,		
	block = :block,
	ordering = :ordering,
	effect = :effect,
	p1 = :p1,
	p2 = :p2,
	p3 = :p3,
	p4 = :p4,
	p5 = :p5,
	p6 = :p6
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'title' => $title,
	'pub' => $pub,
	'content' => $content,
	'content_2' => $content_2,		
	'block' => $block,
	'ordering' => $ordering,
	'effect' => $effect,
	'p1' => $width,
	'p2' => $height,
	'p3' => $color,
	'p4' => $link,
	'p5' => $margin_w,
	'p6' => $margin_h,		
	'id' => $id
));

if($bt_save == LANG_M_FLAT_ROTATE_SAVE){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/flat_rotate/".$id); exit;}

?>