<?php
defined('AUTH') or die('Restricted access');
$head->addFile('/components/photo/admin/section/tmp/style.css');
$head->addFile('/components/photo/admin/section/tmp/main.js');
$head->addFile('/lib/css/font-awesome/css/font-awesome.min.css');
$head->addFile('/js/drag_drop/drag_drop.js');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "photo_item";
		var contextmenu_photo_section = [
			["admin/com/photo/item/edit", "contextmenu_edit", "Редактировать изображение"],
			["admin/com/photo/item/add/'.$d[4].'", "contextmenu_add", "Добавить изображение"],
			["admin/com/photo/item/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/photo/item/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/photo/item/delete", "contextmenu_delete", "Удалить"]
		];
		contextmenu(class_name, contextmenu_photo_section);
	});
</script>
');


function a_com()
{
	global $root, $db, $domain, $d, $photo_settings;
	
	$stmt_section = $db->prepare("SELECT * FROM com_photo_section WHERE id = :id LIMIT 1");
	$stmt_section->execute(array('id' => $d[4]));
	$section = $stmt_section->fetch();
	
	$stmt_item = $db->prepare("SELECT * FROM com_photo_items WHERE section = :section ORDER BY ordering");
	$stmt_item->execute(array('section' => $d[4]));

	$item_out = '';
	
	while($item = $stmt_item->fetch())
	{
		if($item['pub'] == 1){$opacity = '';}else{$opacity = 'style="opacity:0.5;"';}
		$item_out .= '<div '.$opacity.' class="photo_item" data-id="'.$item['id'].'" draggable="true" style="width:'.$photo_settings['x_small'].'px; height:'.($photo_settings['y_small'] + 20).'px;"><div><img src="/files/photo/'.$item['section'].'/'.$item['name'].'.jpg" draggable="false"></div><div class="photo_item_title">'.$item['title'].'</div></div>';
	}

	echo '<h1>'.$section['title'].'</h1>
	<table class="admin_table_2">
		<tr>
			<td style="width:200px;"><a class="section_add" href="/admin/com/photo/section/add/'.$d[4].'">Добавить раздел</a></td>
			<td style="width:200px;"><a class="item_add" href="/admin/com/photo/item/add/'.$d[4].'">Добавить изображение</a></td>
			<td><form enctype="multipart/form-data" method="POST" action="/admin/com/photo/section/ordering"><input id="images_order_button" class="greenbutton" type="submit" value="Сохранить порядок следования"><input id="images_order" name="images_order" type="hidden"><input type="hidden" name="section" value="'.$d[4].'"></form></td>
		</tr>
	</table>	
	<div class="photo_bg">
		<div onclick="window.open(\'/admin/com/photo/section/edit/'.$d[4].'\', \'_self\')"; style="cursor:pointer;">'.$section['text_top'].'</div>
		<div id="drag_trg" class="items_container">'.$item_out.'</div>
		<div  onclick="window.open(\'/admin/com/photo/section/edit/'.$d[4].'\', \'_self\')"; style="cursor:pointer;">'.$section['text_bottom'].'</div>
	</div>';
}


?>
