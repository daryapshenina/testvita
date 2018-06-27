<?php
// DAN 2012
defined('AUTH') or die('Restricted access');

$modules_title_editor = $modules_title;
$modules_titlepub_editor = $modules_titlepub;
$suf_editor = $modules_module_csssuf;

// вывод содержимого модуля
$num = mysql_query("SELECT * FROM `modules` WHERE `module` = 'img4r'") or err_mail("Невозможно сделать выборку из таблицы - mod > img4r");

while($m = mysql_fetch_array($num)):
	$id = $m['id'];
	$title = $m['title'];
	$pub = $m['pub'];
	$enabled = $m['enabled'];
	$description = $m['description'];
	$content = $m['content'];
	$ordering = $m['ordering'];
	$block =	$m['block'];
endwhile;

$image_arr = explode(';', $content);

$image_count = count($image_arr) - 1;
$tab_count = intval($image_count/4);

if ($enabled == '1' && $pub == '1')
{
	$out = '<div class="mod_img4r_main">';

	$i = 0;
	for($t = 1; $t <= $tab_count; $t++)
	{
		$out .= '<div class="mod_img4r_tab">';
			for($r = 1; $r <= 2; $r++)
			{
				$out .= '<div class="mod_img4r_cellrow">';
				$out .= '<div class="mod_img4r_tab">';
					for($c = 1; $c <= 2; $c++)
					{
						$img_arr = explode('#', $image_arr[$i]);
						$img_src = $img_arr[0];
						$img_link = $img_arr[1];
						$img_anchor = $img_arr[2];

						$out .= '<div class="mod_img4r_cell">';

						if(isset($img_link) && $img_link != '')
						{
							$out .= '<a href="'.$img_link.'">';
							$out .= '<div class="mod_img4r_di" style="background-image:url('.$img_src.')"><img src="'.$img_src.'" class="mod_img4r_img" alt="'.$img_anchor.'"></div>';

							if(strlen($img_anchor) > 0)
							{
								$out .= '<div class="mod_img4r_dt">'.$img_anchor.'</div>';
							}

							$out .= '</a>';
						}
						else
						{
							$out .= '<div class="mod_img4r_di" style="background-image:url('.$img_src.')"><img src="'.$img_src.'" class="mod_img4r_img" alt="'.$img_anchor.'"></div>';

							if(strlen($img_anchor) > 0)
							{
								$out .= '<div class="mod_img4r_dt">'.$img_anchor.'</div>';
							}
						}

						$out .= '</div>';




						$i++;
					}
				$out .= '</div>';
			$out .= '</div>';
			}
		$out .= '</div>';
	}

	$out .= '</div>';



	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_img4r" data-id="">'.$out.'</div>';}
	else {echo $out;}
}

?>
