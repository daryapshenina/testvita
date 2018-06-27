<?php
// DAN обновление - январь 2014
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

$modules_title_editor = $modules_title;
$modules_titlepub_editor = $modules_titlepub;
$suf_editor = $modules_module_csssuf;

if ($modules_pub == "1")
{
	$breadcrumbs_path = '';

	if($d[0] == 'shop' && $d[1] == 'basket')
	{
		$breadcrumbs_path = '<img src="/modules/breadcrumbs/frontend/img/arrow.png" alt="" /> '.LANG_M_BREADCRUMBS_BASKET;
	}
	elseif($d[0] == 'search')
	{
		$breadcrumbs_path = '<img src="/modules/breadcrumbs/frontend/img/arrow.png" alt="" /> '.LANG_M_BREADCRUMBS_SEARCH;
	}
	else
	{
		$i = 1;
		$c = count($menu->parentArr);	// количество уровней

		foreach ($menu->parentArr as $item)
		{
			if($i == $c && $d[1] != 'item'){$breadcrumbs_path .= '<img src="/modules/breadcrumbs/frontend/img/arrow.png" alt="" /> <span class="mod_breadcrumbs_link_act">'.$item['name'].'</span>';}
			else {$breadcrumbs_path .= '<img src="/modules/breadcrumbs/frontend/img/arrow.png" alt="" /> <a href="/'.$item['url'].'" class="mod_breadcrumbs_link">'.$item['name'].'</a> ';}
			$i++;
		}
	}

	$breadcrumbs_path = '<span class="mod_breadcrumbs_you">'.LANG_M_BREADCRUMBS_YOU_ARE_HERE.':</span> '.$breadcrumbs_path;

	$out = '<div class="mod_breadcrumbs">'.$breadcrumbs_path.'</div>';

	// frontend редактирование
	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_breadcrumbs" data-id="'.$modules_id.'">'.$out.'</div>';}
	else {echo $out;}
}

?>