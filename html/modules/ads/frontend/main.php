<?php
defined('AUTH') or die('Restricted access');
include_once $_SERVER['DOCUMENT_ROOT'].'/modules/ads/classes/modAds.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/components/ads/classes/adsSectionItems.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/components/ads/frontend/section/tmp/tmp.php';

$m_s = unserialize($m['content']);

if($m_s->title_pub) $title_out = '<div class="mod_title">'.$m_s->title.'</div>';
	else $title_out = '';

$section_items = new adsSectionItems;

$section_items->setPub(1);
$section_items->setContent();
$section_items->setLimit(0, $m_s->quantity);
$items = $section_items->getItems();

$out = '';

foreach($items as $item)
{	
	if($item['image'] == 1)
	{
		$floor_id = 1000 * floor($item['user_id']/1000); // тысячная папка
		$path = '/files/ads/'.$floor_id.'/'.$item['user_id'];
		$item['image_out'] = '<img class="mod_ads_image" alt="'.$item['title'].'" src="'.$path.'/'.$item['id'].'.jpg">';
	}
	else 
	{
		$item['image_out'] = '<img class="mod_ads_image" alt="" src="/components/ads/frontend/my/tmp/nophoto.jpg">';
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
		if(!empty($item['content'])) $item['content_out'] = '<div class="mod_ads_text">'.$item['content'].'</div>';
			else $item['content_out'] = '';

		$item['date_out'] = '<div class="mod_ads_date_wrap">'.$item['date_c'].'</div>';
	}

	$out .= mod_ads_section_item($item);
}


// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = ' edit_mode';
	$edit_data = 'data-type="mod_ads" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}

echo '
	<div '.$edit_data.' class="mod_ads_container'.$edit_class.'">
		'.$title_out.'
		<div class="mod_ads_item_container">'.$out.'</div>
	</div>
';

function mod_ads_section_item($item)
{

	$out = '<div class="mod_ads_item_wrap"><div class="mod_ads_image_wrap">'.$item['image_out'].'</div><div class="mod_ads_text_wrap"><a class="mod_ads_text_title" href="/ads/item/'.$item['id'].'">'.$item['title'].'</a>'.$item['content_out'].'<div class="mod_ads_date">'.$item['date_out'].'</div></div></div>';
	return $out;
}


?>