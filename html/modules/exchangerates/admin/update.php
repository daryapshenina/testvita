<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d['3']);

if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else{$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else{$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else{$mod_titlepub = 0;}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else{$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else{$mod_ordering = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'


// Обновляем данные в таблице "modules"
$query_updatedit_modeule_special = "UPDATE `modules` SET `title` = '$mod_title', `pub` = '$mod_pub', `titlepub` = '$mod_titlepub', `content` = '$mod_content', `block` = '$mod_block', `ordering` = '$mod_ordering' WHERE `id` = '$mod_id' LIMIT 1 ;";

$sql_module_special = mysql_query($query_updatedit_modeule_special) or die ("Невозможно обновить данные");

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET 
	title = :title, 
	pub = :pub, 
	titlepub = :titlepub, 
	block = :block, 
	ordering = :ordering
	WHERE id = :id LIMIT 1
");

$stmt_update->execute(array(
	'title' => $mod_title,
	'pub' => $mod_pub,
	'titlepub' => $mod_titlepub,
	'block' => $mod_block,
	'ordering' => $mod_ordering,
	'id' => $mod_id
));


if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/exchangerates/".$mod_id); exit;}

?>