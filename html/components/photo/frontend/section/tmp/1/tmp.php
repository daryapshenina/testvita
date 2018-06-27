<?php
defined('AUTH') or die('Restricted access');

function component()
{
	global $root, $db, $domain, $d, $section, $photo_settings, $frontend_edit;

	$stmt_item = $db->prepare("SELECT * FROM com_photo_items WHERE section = :section AND pub = '1' ORDER BY ordering");
	$stmt_item->execute(array('section' => $d[2]));

	$item_out = '';


	while($item = $stmt_item->fetch())
	{
		if($item['link'] == ''){$a = '<a href="/files/photo/'.$item['section'].'/'.$item['name'].'_.jpg" class="show">';}
		else {$a = '<a href="'.$item['link'].'">';}

		$item_title_out = '';


		if($item['link'] != '')
		{
			$item_title_out = '<a href="'.$item['link'].'" class="photo_item_title_a">'.$item['title'].'</a>';
		}
		else
		{
			// $item_title_out .= '<div class="photo_item_title_2"><i id="photo_item_like_'.$item['id'].'" onclick="photo_like('.$item['id'].');" class="fa fa-heart photo_like">'.$item['likes'].'</i></div><div class="photo_item_title_1">'.$item['title'].'</div>';
			if($item['title'] != ''){$item_title_out = '<div class="photo_item_title">'.$item['title'].'</div>';}else{$item_title_out = '';}
		}

		if(strlen($item['text']) > 0)
		{
			$item_description = '<div class="photo_item_text">' . $item['text'] . '</div>';
		}
		else
		{
			$item_description = '';
		}

		if($frontend_edit == 1){$edit_item = ' data-id="'.$item['id'].'"';}else{$edit_item = '';}

		$item_out .= '<div'.$edit_item.' class="photo_item" style="width:'.$photo_settings['x_small'].'px;">';
		$item_out .= 	$a;
		$item_out .= 		'<img src="/files/photo/'.$item['section'].'/'.$item['name'].'.jpg" alt="'.$item['title'].'">';
		$item_out .= 	'</a>';
		$item_out .= 	$item_title_out;
		$item_out .= 	$item_description;
		$item_out .= '</div>';
	}

	if($frontend_edit == 1)
	{
		$edit_data = ' data-type="com_photo" data-id="'.$d[2].'"';
		$edit_class = ' edit_mode';
	}
	else
	{
		$edit_data = '';
		$edit_class = '';
	}

	echo '<div'.$edit_data.' class="photo_container'.$edit_class.'"><h1 class="photo_title">'.$section['title'].'</h1><div class="photo_bg"><div class="photo_text_top">'.$section['text_top'].'</div><div class="photo_items_container">'.$item_out.'</div><div class="photo_text_bottom">'.$section['text_bottom'].'</div></div></div>';
}

?>