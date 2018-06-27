<?php
// 	Выводит товары интернет магазина
defined('AUTH') or die('Restricted access');

if(!isset($shopSettings))
{
	include_once($root."/components/shop/classes/classShopSettings.php");	
	$s = new classShopSettings;
	$shopSettings = unserialize($s->settings);	
}

// Авторизация пользователя
include_once($root."/classes/Auth.php");
$u = Auth::check();


if(isset($shopItem)){unset($shopItem);}
$shopItem = new classShopSectionItem($shopSettings);


// Цены пользователя
if(!empty($u))
{
	$stmt_pu = $db->prepare("SELECT u.price_type_id FROM com_shop_price_user u JOIN com_shop_price_type t ON t.id = u.price_type_id  WHERE user_id = :user_id LIMIT 1");
	$stmt_pu->execute(array('user_id' => $u));
	$pt_id = $stmt_pu->fetchColumn();

	$shopItem->setPriceTypeId($pt_id);
}
else
{
	$price_type = '';
}


if($m['p10'] == 1)
{
	$wrapper_1 = '<div id="mod_shop_wrap_'.$m['id'].'" class="mod_shop_wrap">';
	$wrapper_2 = '</div>';
	$navigation = '<div id="mod_shop_scroll_prev_'.$m['id'].'" class="mod_shop_scroll_prev" onclick="f_mod_shop_scroll_prev(\''.$m['id'].'\')"></div><div id="mod_shop_scroll_next_'.$m['id'].'" class="mod_shop_scroll_next" onclick="f_mod_shop_scroll_next(\''.$m['id'].'\')"></div>';
}
else
{
	$wrapper_1 = '';
	$wrapper_2 = '';
	$navigation = '';
}


if($m['titlepub'] == "1")
{
	$title_out = '<div class="mod-title'.$m['module_csssuf'].'">';

	if($m['p4'] != '0' && $m['p7'] == '1')
	{
		switch($m['p4'])
		{
			case 'discount':
				$title_out .= '<a class="mod_shop_title_link" href="/shop/section/all/?discount=1" rel="nofollow">'.$m['p5'].'</a>';
				break;

			case 'new':
				$title_out .= '<a class="mod_shop_title_link" href="/shop/section/all/?new=1" rel="nofollow">'.$m['p5'].'</a>';
				break;

			case 'hit':
				$title_out .= '<a class="mod_shop_title_link" href="/shop/section/all/?hit=1" rel="nofollow">'.$m['p5'].'</a>';
				break;

		default:
			$title_out .= '<a class="mod_shop_title_link" href="/shop/section/'.$m['p4'].'" rel="nofollow">'.$m['p5'].'</a>';
		}
	}
	else
	{
		$title_out .= $m['title'];
	}

	$title_out .= '</div>';
}
else {$title_out = '';}



$arrayCategory = explode(';', $m['p3']);

if(count($arrayCategory) > 0)
{
	foreach($arrayCategory as $iter)
	{
		if($iter != ''){$shopItem->addSection($iter);}
	}
}

$shopItem->setQuantity($m['p2']);
$shopItem->setAllCategoryIfNotAdd(true);
$shopItem->setViewItemWithoutImage(true);
$shopItem->setMode($m['p6']);
$shopItem->setTypeOut(1); // выводить как модуль


if($m['p1'] == '0'){$item_out =  $shopItem->viewItemsRandom();} // Если 0 то выводим случайные товары
else{$item_out = $shopItem->viewItems(); } // Иначе последнии товары

// Подвал модуля
// вывод ссылки "Все товары"
$link_out = '';

if($m['p4'] != '0' && strlen($m['p5']) > 0 && $m['p7'] == '0')
{
	$link_out .= '<div>&nbsp;</div><div class="mod_shop_link_block">';

	switch($m['p4'])
	{
		case 'discount':
			$link_out .= '<a class="mod_shop_section_link" href="/shop/section/all?discount=1" rel="nofollow">'.$m['p5'].'</a>';
			break;

		case 'new':
			$link_out .= '<a class="mod_shop_section_link" href="/shop/section/all?new=1" rel="nofollow">'.$m['p5'].'</a>';
			break;

		case 'hit':
			$link_out .= '<a class="mod_shop_title_link" href="/shop/section/all?hit=1" rel="nofollow">'.$m['p5'].'</a>';
			break;


		default:
			$link_out .= '<a class="mod_shop_section_link" href="/shop/section/'.$m['p4'].'" rel="nofollow">'.$m['p5'].'</a>';
	}

	$link_out .= '</div>';
}

$out = 	'<div class="mod-main'.$m['module_csssuf'].' mod_shop_main">';
$out .= 	'<div class="mod-top'.$m['module_csssuf'].'">'.$title_out.'</div>';
$out .= 	'<div id="mod_shop_container_'.$m['id'].'" class="mod-mid'.$m['module_csssuf'].'">';
$out .= 		'<div id="mod_shop_frame_'.$m['id'].'" class="mod_shop_frame mod-padding'.$m['module_csssuf'].'">'.$navigation;
$out .= 			$wrapper_1.$item_out.$wrapper_2;
$out .= 		'</div>';
$out .= 		$link_out;
$out .= 	'</div>';
$out .= '</div>';

if($m['p10'] == '1')
	$out .= '<script type="text/javascript">f_mod_shop_scroll("'.$m['id'].'");window.addEventListener("resize", function(){f_mod_shop_scroll("'.$m['id'].'")});</script>';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_shop" data-id="'.$m['id'].'">'.$out.'</div>';}else{echo $out;}

unset($shopItem);

?>
