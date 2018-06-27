<?php
defined('AUTH') or die('Restricted access');
include($root."/components/ads/classes/AdsSection.php");
$head->addFile('/components/ads/admin/section/tmp/edit.css');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/css/imgareaselect-default.css');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.min.js');
$head->addFile('/lib/image_resize/jquery.imgareaselect-0.9.10/scripts/jquery.imgareaselect.pack.js');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.css');
$head->addFile('/lib/image_resize/IMAGE_RESIZE.js');
$head->addFile('/components/ads/admin/section/tmp/edit.js');


if($d[4] == 'add')
{
	$m['parent'] = 0;
}
else
{
	$stmt_m = $db->prepare("SELECT * FROM menu WHERE id_com = :section_id AND component = 'ads' AND p1 = 'section' LIMIT 1");
	$stmt_m->execute(array('section_id' => $d[5]));
	$m = $stmt_m->fetch();
}

$head->addCode('
<script language="JavaScript">
	function menu_type_select(menu_id)
	{
		if (document.getElementById("menu_type_left").selected == true) menu_type_ajax("left",menu_id);
		if (document.getElementById("menu_type_top").selected == true) menu_type_ajax("top",menu_id);
	}

	function menu_type_ajax(type,m_id)
	{
		var req = getXmlHttp()
		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
			{
				if(req.status == 200)
				{
					document.getElementById("menu_parent").innerHTML = req.responseText;
				}
			}

		}

		req.open(\'GET\', \'/administrator/modules/menu_tree.php?type=\' + type + \'&menuid=\' + m_id + \'&menuparent=\' + '.$m['parent'].', true);
		req.send(null);
		document.getElementById("menu_parent").innerHTML = "<div align=\"left\"><img src=\"/administrator/tmp/images/loading.gif\" /></div>";
	}
</script>
');


function a_com()
{
	global $root, $db, $domain, $m, $d;

	if($d[4] == 'add')
	{
		$title = 'Добавить раздел';
		$act = 'insert';

		$menu_top_selected = '';
		$menu_left_selected = 'selected';

		$section = new AdsSection();
		$section->pub_checked = 'checked';

		$m['id'] = 0;
		$m['name'] = '';
		$m['parent'] = 0;
		$m['ordering'] = 1;
		
		$img_src = '/components/ads/admin/section/tmp/no_photo.png';
	}
	else
	{
		$title = 'Редактировать раздел';

		$menu_top_selected = '';
		$menu_left_selected = '';
		if($m['menu_type'] = 'top') $menu_top_selected = 'selected';
		if($m['menu_type'] = 'left') $menu_left_selected = 'selected';
			
		$stmt = $db->prepare("SELECT * FROM com_ads_section WHERE id = :section_id LIMIT 1");
		$stmt->execute(array('section_id' => $d[5]));
		$s = $stmt->fetch();
		$section = unserialize($s['options']);
		$section->id = $s['id'];
		$section->title = $s['title'];
		if($s['pub'] == 1) $section->pub_checked = 'checked';
		else $section->pub_checked = '';

		if($s['image'] == 1) $img_src = '/files/ads/sections/'.$section->id.'.jpg';
		else $img_src = '/components/ads/admin/section/tmp/no_photo.png';

		$act = 'update/'.$s['id'];
		
		$stmt_url = $db->prepare("SELECT sef FROM url WHERE url = :url LIMIT 1");
		$stmt_url->execute(array('url' => 'ads/section/'.$section->id));		
				
		$section->sef = $stmt_url->fetchColumn();
	}

	include($root."/components/ads/admin/section/tmp/edit_tmp.php");
}

?>