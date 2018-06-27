<?php
defined('AUTH') or die('Restricted access');

// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = ' edit_mode';
	$edit_data = 'data-type="mod_photo_s" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}

if($m['p10'] == ''){$img_out = 'НЕТ ФОТО';}else{$img_out = '<img class="mod_photo_s_img" src="/files/modules/photo_s/'.$m['p10'].'" alt="">';}

if($m['p1'] == ''){echo '<div '.$edit_data.' class="mod_photo_s_container col_d_25 col_n_25 col_t_50 col_p_100'.$edit_class.'">';} else {echo '<a '.$edit_data.' href="'.$m['p1'].'" class="mod_photo_s_container col_d_25 col_n_25 col_t_50 col_p_100'.$edit_class.'">';}
echo 		'<div class="mod_photo_s_content" style="margin:'.$m['margin_h'].'px '.$m['margin_w'].'px;">'.
			$img_out.
			'<div class="mod_photo_s_hover" style="background:'.$m['bg_color'].';">'.
				'<div class="mod_photo_s_table">'.
					'<div class="mod_photo_s_td">'.
						'<div class="mod_photo_s_title">'.$m['content'].'</div>'.
						'<div class="mod_photo_s_hr"></div>'.
						'<div class="mod_photo_s_text">'.$m['content_2'].'</div>'.
					'</div>'.
				'</div>'.
			'</div>'.
		'</div>';
if($m['p1'] == ''){echo '</div>';} else {echo '</a>';}


?>