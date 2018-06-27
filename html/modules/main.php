<?php
defined('AUTH') or die('Restricted access');

$stmt_mod = $db->query("SELECT * FROM modules WHERE enabled = '1' AND pub > '0' AND block != '' ORDER BY ordering");
$mod_arr = $stmt_mod->fetchAll();

$incl = array('form', 'flat_rotate');

foreach($mod_arr as $m)
{
	if(file_exists($root.'/modules/'.$m['module'].'/frontend/include.php'))
	{
		if(in_array($m['module'], $incl)) // Для некоторых модулей include.php надо включать для всех случаев
		{
			include($root.'/modules/'.$m['module'].'/frontend/include.php');
		}
		else
		{
			include_once($root.'/modules/'.$m['module'].'/frontend/include.php');			
		}
	}
}



// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ===========================================================

include_once $root."/classes/MobileDetector.php";

const MODULE_PUBLIC_OFF = 0;
const MODULE_PUBLIC_ON = 1;
const MODULE_PUBLIC_ONLY_PC = 2;
const MODULE_PUBLIC_ONLY_PHONE = 3;

function block($block_out)
{
	global $db, $mod_arr, $root, $domain, $site, $url_arr, $d, $menu, $shopSettings, $frontend_edit, $asd, $utm;

	foreach($mod_arr as $m)
	{
		if($m['block'] == $block_out)
		{
			// ОСТАВЛЯЕМ ДЛЯ СОВМЕСТИМОСТИ
			$modules_id = $m['id'];
			$modules_title = $m['title'];
			$modules_module = $m['module'];
			$modules_module_csssuf = $m['module_csssuf'];
			$modules_pub = $m['pub'];
			$modules_titlepub = $m['titlepub'];
			$modules_enabled = $m['enabled'];
			$modules_description = $m['description'];
			$modules_content = $m['content'];
			$modules_p1 = $m['p1'];
			$modules_p2 = $m['p2'];
			$modules_p3 = $m['p3'];
			$modules_p4 = $m['p4'];
			$modules_p5 = $m['p5'];
			$modules_p6 = $m['p6'];
			$modules_p7 = $m['p7'];
			$modules_p8 = $m['p8'];
			$modules_p9 = $m['p9'];
			$modules_p10 = $m['p10'];

			switch($m['pub'])
			{
				case MODULE_PUBLIC_ONLY_PHONE:
				{
					$device = MobileDetector::getDevice();
					if($device != NULL)
						include($root."/modules/".$modules_module."/frontend/main.php");
				} break;

				case MODULE_PUBLIC_ONLY_PC:
				{
					$device = MobileDetector::getDevice();
					if($device == NULL)
						include($root."/modules/".$modules_module."/frontend/main.php");
				} break;

				case MODULE_PUBLIC_ON:
				{
					include($root."/modules/".$modules_module."/frontend/main.php");
				} break;
			}
		}
	}
}

// ======== / загрузка блоков вывода ========================================================







?>