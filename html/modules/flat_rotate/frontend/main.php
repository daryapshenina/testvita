<?php
defined('AUTH') or die('Restricted access');

// frontend редактирование
if($frontend_edit == 1)
{
	$edit_class = 'edit_mode ';	
	$edit_data = 'data-type="mod_flat_rotate" data-id="'.$m['id'].'"';
}
else
{
	$edit_class = '';	
	$edit_data = '';
}

echo '
	<a '.$edit_data.' href="'.$m['p4'].'" class="'.$edit_class.'mod_flat_rotate_'.$m['effect'].'" style="width:'.$m['p1'].'px; height:'.$m['p2'].'px; margin:'.$m['p6'].'px '.$m['p5'].'px;">
		<div class="mod_flat_rotate_container_'.$m['effect'].'" style="width:'.$m['p1'].'px; height:'.$m['p2'].'px;">
			<div class="mod_flat_rotate_front_'.$m['effect'].'" style="width:'.($m['p1'] - 40).'px; height:'.($m['p2'] - 40).'px;">'.$m['content'].'</div>
			<div class="mod_flat_rotate_back_'.$m['effect'].'" style="width:'.($m['p1'] - 40).'px; height:'.($m['p2'] - 40).'px; background-color:'.$m['p3'].';">'.$m['content_2'].'</div>
		</div>
	</a>
';

?>
