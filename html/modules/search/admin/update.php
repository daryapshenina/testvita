<?php
// Обновить данные
$id  = intval($d[3]);

if(isset($_POST["title"])){$title = htmlspecialchars($_POST["title"]);} else{$title = '';}
if(isset($_POST["pub"])){$pub = intval($_POST["pub"]);} else{$pub = 0;}
if(isset($_POST["titlepub"])){$titlepub = intval($_POST["titlepub"]);} else{$titlepub = 0;}
if(isset($_POST["block"])){$block = htmlspecialchars($_POST["block"]);} else{$block = '';}
if(isset($_POST["ordering"])){$ordering = intval($_POST["ordering"]);} else{$ordering = 0;}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else{$bt_save = '';} // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else{$bt_prim = '';} // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else{$bt_none = '';} // кнопка 'Отменить'

// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/modules"); exit;}

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET
	title = :title,
	pub = :pub,
	titlepub = :titlepub,
	block = :block,	
	ordering = :ordering
	WHERE id = :id AND module = 'search'
");

$stmt_update->execute(array(
'title' => $title,
'pub' => $pub,
'titlepub' => $titlepub,
'block' => $block,
'ordering' => $ordering,
'id' => $id
));


if($bt_save == 'Сохранить'){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/search/".$id); exit;}

?>