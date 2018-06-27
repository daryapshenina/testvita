<?php
defined('AUTH') or die('Restricted access');

if($m['effect_a'] != ''){$effect_a = ' '.$m['effect_a'];} else{$effect_a = '';}

// frontend редактирование
if(isset($frontend_edit ) && $frontend_edit == 1)
{
	$edit_class = ' edit_mode';
	$edit_data = 'data-type="mod_circle" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';
	$edit_data = '';
}

if($m['p10'] == ''){$img_out = 'НЕТ ФОТО';}else{$img_out = '<img class="mod_photo_s_img" src="/files/modules/photo_s/'.$m['p10'].'" alt="">';}

echo '<div '.$edit_data.' class="mod_circle_container'.$effect_a.$edit_class.'" style="padding:'.$m['padding_h'].'px '.$m['padding_w'].'px;width:'.$m['width_f'].'px;height:'.$m['height_f'].'px;">';
	if($m['p2'] == ''){echo '<div class="mod_circle_wrap" style="width:'.$m['width_f'].'px;height:'.$m['width_f'].'px;">';} else{echo '<a href="'.$m['p2'].'" class="mod_circle_wrap" style="width:'.$m['width_f'].'px;height:'.$m['width_f'].'px;">';}	
		echo '<div class="mod_circle_item" style="background-image:url(/files/modules/circle/'.$m['p10'].');">';
			echo '<div class="mod_circle_2">';
				echo '<div class="mod_circle_3" style="background-image:url(/files/modules/circle/'.$m['p10'].');"></div>';	
				echo '<div class="mod_circle_back" style="background:'.$m['bg_color'].';"><div class="mod_circle_text" style="font-size:'.$m['p1'].'px;color:'.$m['color'].'">'.$m['content'].'</div></div>';
			echo '</div>';				
		echo '</div>';
	if($m['p2'] == ''){echo '</div>';}else{echo '</a>';}
	echo '<div class="mod_circle_text_2">'.$m['content_2'].'</div>';
echo '</div>';

?>