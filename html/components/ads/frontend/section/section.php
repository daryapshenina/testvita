<?php
defined('AUTH') or die('Restricted access');
include_once $root."/components/ads/classes/adsSectionItems.php";
include_once $root."/components/ads/frontend/section/tmp/tmp.php";
$head->addFile('/components/ads/frontend/section/tmp/section.css');


function component()
{
	global $root, $db, $d;

	$stmt_section = $db->prepare("SELECT title FROM com_ads_section WHERE id = :id");
	$stmt_section->execute(array('id' => $d[2]));
	$section = $stmt_section->fetch();

	$limit = 100;

	if(isset($_GET['page'])) $page = intval($_GET['page'] * $limit);
		else $page = 0;

	$section_items = new adsSectionItems;

	$section_items->setPub(1);
	$section_items->setSection($d[2]);
	$section_items->setContent();
	$section_items->setLimit($page,$limit);
	$items = $section_items->getItems();

	$out = '';

	foreach($items as $item)
	{	
		if($item['image'] == 1)
		{
			$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
			$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];
			$item['image_out'] = '<img class="ads_image" alt="'.$item['title'].'" src="'.$path.'/'.$item['id'].'.jpg">';
		}
		else 
		{
			$item['image_out'] = '<img class="ads_image" alt="" src="/components/ads/frontend/my/tmp/nophoto.jpg">';
		}

		if(MobileDetector::getDevice())
		{
			$item['content_out'] = '';
			$item['date_out'] = '';
		}
		else 
		{
			// Обрезаем текст по словам.
			if(mb_strlen($item['content']) > 300) $item['content'] = mb_substr($item['content'], 0, mb_strrpos(mb_substr($item['content'], 0, 300), ' ')).'...';
			$item['content_out'] = '<div class="ads_text">'.$item['content'].'</div>';

			$item['date_out'] = '<div class="ads_date_wrap">'.$item['date_c'].'</div>';
		}

		$out .= ads_section_item($item);
	}

	echo'<h1>'.$section['title'].'</h1>'.$out;	

}

?>