<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[4]);

if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else{$mod_titlepub = 0;}
if(isset($_POST["text"])){$mod_text = htmlspecialchars(strip_tags($_POST["text"]));}else {$mod_text = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["block"])){$mod_block = strip_tags($_POST["block"]);} else{$mod_block = '';}
if(isset($_POST["url_select"])){$mod_url_select = intval($_POST["url_select"]);} else{$mod_url_select = 0;}
if(isset($_POST["url"])){$mod_url = trim(strip_tags($_POST["url"]));} else{$mod_url = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else{$mod_ordering = 0;}
if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'


// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	pub = :pub,
	content = :content,
	block = :block,
	p1 = :p1,
	p2 = :p2,
	ordering = :ordering
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'pub' => $mod_pub,
	'block' => $mod_block,
	'content' => $mod_text,
	'ordering' => $mod_ordering,
	'p1' => $mod_url_select,
	'p2' => $mod_url,	
	'id' => $mod_id
));

if($bt_save != ''){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/authorization/".$mod_id); exit;}



?>