<?php
defined('AUTH') or die('Restricted access');
include_once $root."/components/ads/classes/adsSectionItems.php";
$head->addFile('/lib/contextmenu/contextmenu.js');

$head->addCode('
<script type="text/javascript">
	DAN_ready(function()
	{
		class_name = "contextmenu_ads";
		var contextmenu_ads = [
			["admin/com/ads/item/edit", "contextmenu_edit", "Редактировать"],			
			["admin/com/ads/item/pub", "contextmenu_pub", "Опубликовать"],
			["admin/com/ads/item/unpub", "contextmenu_unpub", "Скрыть"],
			["admin/com/ads/item/delete", "contextmenu_delete", "Удалить"]
		];
		CONTEXTMENU.add(class_name, contextmenu_ads);
	});
</script>
');


function a_com()
{
	global $root, $db, $d;

	$stmt_section = $db->prepare("SELECT title FROM com_ads_section WHERE id = :id");
	$stmt_section->execute(array('id' => $d[4]));
	$section = $stmt_section->fetch();

	$limit = 100;

	if(isset($_GET['page'])) $page = intval($_GET['page'] * $limit);
		else $page = 0;

	$section_items = new adsSectionItems;

	$section_items->setPub('all');
	$section_items->setSection($d[4]);
	$section_items->setLimit($page,$limit);
	$items = $section_items->getItems();

	$out = '';

	foreach($items as $item)
	{	
		if ($item['pub'] == 1)
		{
			$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
			$classmenu = "menu_pub";
		}
		else 
		{
			$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
			$classmenu = "menu_unpub";
		}

		if($item['image'] == 1)
		{
			$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
			$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];
			$item['image_out'] = '<img class="'.$classmenu.'" style="width:80px;" alt="'.$item['title'].'" src="'.$path.'/'.$item['id'].'.jpg?"'.rand().'>';
		}
		else 
		{
			$item['image_out'] = '<img class="'.$classmenu.'" style="width:80px;" alt="" src="/components/ads/frontend/my/tmp/nophoto.jpg">';
		}

		$out .= '
		<tr>
			<td class="contextmenu_ads" data-id="'.$item['id'].'">'.$item['id'].'</td>			
			<td class="contextmenu_ads" data-id="'.$item['id'].'">'.$item['image_out'].'</td>
			<td class="contextmenu_ads" data-id="'.$item['id'].'"><a class="sitemenuitem '.$classmenu.'" id="'.$item['id'].'" href="/admin/com/ads/item/edit/'.$item['id'].'" title="выводит товар">'.$item['title'].'</a></td>
			<td class="contextmenu_ads" data-id="'.$item['id'].'"><span class="'.$classmenu.'">'.$item['date_c'].'</span></td>
			<td class="contextmenu_ads" data-id="'.$item['id'].'">'.$pub_x.'</td>
		</tr>		
		';
	}


	echo '
	<h1>'.$section['title'].'</h1>
	<table class="admin_table even_odd">
		<tbody>
			<tr>
				<th style="width:50px;"></th>
				<th style="width:80px;"></th>
				<th>Объявления:</th>
				<th style="width:150px;">Дата</th>
				<th style="width:50px;" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные" align="center">Пб.</th>
			</tr>
			'.$out.'
		</tbody>
	</table>	
	';
}

?>