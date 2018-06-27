<?php
defined('AUTH') or die('Restricted access');

$related = $_POST['related'];
$new = $_POST['new'];
$sale = $_POST['sale'];
$hit = $_POST['hit'];
$rating = $_POST['rating'];
$order = $_POST['order'];

$new = check_stickers($new);
$sale = check_stickers($sale);
$hit = check_stickers($hit);
$order = check_stickers($order);

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// Условие - отменить
if ($bt_none != ''){Header ("Location: /admin/com/shop"); exit;}

$shopSettings->related_items = $related;
$shopSettings->sticker_new = $new;
$shopSettings->sticker_sale = $sale;
$shopSettings->sticker_hit = $hit;
$shopSettings->sticker_rating = $rating;
$shopSettings->sticker_order = $order;

$s_serialize = serialize($shopSettings);

$stmt = $db->prepare("UPDATE com_shop_settings SET parametr = :s_serialize WHERE name = 'settings'");
$stmt->execute(array('s_serialize' => $s_serialize));


function check_stickers($_data)
{
	$data = mb_ereg_replace("[^A-za-zА-Яа-я0-9\.\-\ \!]","",$_data);
	return $data;
}
	
Header ("Location: /admin/com/shop"); exit;

?>