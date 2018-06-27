<?php
defined('AUTH') or die('Restricted access');

function ads_section_item($item)
{

	$out = '<div class="ads_item_container"><div class="ads_image_wrap">'.$item['image_out'].'</div><div class="ads_text_wrap"><a class="ads_text_title" href="/ads/item/'.$item['id'].'">'.$item['title'].'</a>'.$item['content_out'].$item['date_out'].'</div></div>';
	return $out;
}

?>