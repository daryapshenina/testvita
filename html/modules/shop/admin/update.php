<?php
defined('AUTH') or die('Restricted access');

include_once __DIR__.'/lang/'.LANG.'.php';

$act = $admin_d3;
if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else {$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else {$mod_titlepub = 0;}
$mod_type = intval($_POST["type"]);
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else {$mod_block =  '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else {$mod_ordering = '';}
if(isset($_POST["ptype_vivod"])){$mod_vivodchecked = intval($_POST["ptype_vivod"]);} else {$mod_vivodchecked = '';}
if(isset($_POST["pkoltov"])){$mod_kolvotov = intval($_POST["pkoltov"]);} else {$mod_kolvotov = '';}
if(isset($_POST["section_link"])){$mod_section_link = htmlspecialchars($_POST["section_link"]);} else {$mod_section_link = '';}
if(isset($_POST["link_name"])){$mod_link_name = htmlspecialchars($_POST["link_name"]);} else {$mod_link_name = '';}
if(isset($_POST["link_position"])){$mod_link_position = intval($_POST["link_position"]);} else {$mod_link_position = 0;}
if(isset($_POST["mode"])){$mod_mode = intval($_POST["mode"]);} else {$mod_mode = '';}

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}

$mod_razdel_save_bd = '';

// Получаем массив ид категорий
if (isset($_POST["prazdel"]))
{
	$mod_prazdel = ($_POST["prazdel"]);

	// проверяем есть ли что нибудь в массиве
	if (!empty($mod_prazdel))
	{
		$c = 0;
		foreach ($mod_prazdel as $value)
		{
			$c++;
			// разделы через точку с запятой для БД
			if ($c == 1){$mod_razdel_save_bd .= $value;} else{$mod_razdel_save_bd .= ";".$value;}
		}
	}
	else
	{
		$mod_razdel_save_bd = '0';
	}
}


// Условие публикации
if (!isset($m['pub']) || $m['pub'] == ""){$m['pub'] = "0";}

// Обновляем данные в таблице "modules"
$stmt_update = $db->prepare("
	UPDATE modules SET 
	title = :title, 
	pub = :pub, 
	titlepub = :titlepub, 
	p1 = :p1, 
	p2 = :p2, 
	p3 = :p3,
	p4 = :p4,
	p5 = :p5,
	p6 = :p6,
	p7 = :p7,
	p10 = :p10,
	block = :block, 
	ordering = :ordering
	WHERE module = 'shop' 
	AND id = :id 
	LIMIT 1
");

$stmt_update->execute(array(
	'title' => $mod_title, 
	'pub' => $mod_pub, 
	'titlepub' => $mod_titlepub, 
	'p1' => $mod_vivodchecked,
	'p2' => $mod_kolvotov,
	'p3' => $mod_razdel_save_bd,
	'p4' => $mod_section_link,
	'p5' => $mod_link_name,
	'p6' => $mod_mode,
	'p7' => $mod_link_position,
	'p10' => $mod_type,
	'block' => $mod_block,
	'ordering' => $mod_ordering,
	'id' => $d[4]
));

if($bt_save == LANG_M_SHOP_SAVE){Header ("Location: /admin/modules/"); exit;}
else {Header ("Location: /admin/modules/shop/".$admin_d4); exit;}


?>
