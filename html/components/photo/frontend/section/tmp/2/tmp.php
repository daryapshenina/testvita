<?php
defined('AUTH') or die('Restricted access');

function component()
{
	global $root, $db, $domain, $d, $section, $photo_settings, $frontend_edit;

	$stmt_item = $db->prepare("SELECT * FROM com_photo_items WHERE section = :section  AND pub = '1' ORDER BY ordering");
	$stmt_item->execute(array('section' => $d[2]));
	
	$item_out = '';


	
	while($item = $stmt_item->fetch())
	{
		if($frontend_edit == 1){$edit_item = ' data-id="'.$item['id'].'"';}else{$edit_item = '';}

		if($item['link'] == ''){$a = '<a'.$edit_item.' href="/files/photo/'.$item['section'].'/'.$item['name'].'_.jpg" class="show photo_item">';}
		else {$a = '<a'.$edit_item.' href="'.$item['link'].'" target="_blank" class="photo_item">';}
		$item_out .= $a.
			'<img class="photo_img" src="/files/photo/'.$item['section'].'/'.$item['name'].'.jpg" alt="">'.
			'<div class="photo_hover">'.
				'<div class="photo_table">'.
					'<div class="photo_td">'.
						'<div class="photo_item_title">'.$item['title'].'</div>'.
						'<div class="photo_hr"></div>'.
						'<div class="photo_item_text">'.$item['text'].'</div>'.
					'</div>'.
				'</div>'.
			'</div>'.
		'</a>';			
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