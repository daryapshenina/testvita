<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST["none"])){Header ("Location: /admin/com/shop"); exit;}

$shopSettings->shop_name = trim(htmlspecialchars($_POST['shop_name']));
$shopSettings->company_name = trim(htmlspecialchars($_POST['company_name']));
$shopSettings->delivery = trim(htmlspecialchars($_POST['delivery']));
$shopSettings->yml_key = trim(htmlspecialchars($_POST['yml_key']));
$shopSettings->x_small = intval($_POST['x_small']);
$shopSettings->y_small = intval($_POST['y_small']);
$shopSettings->x_big = intval($_POST['x_big']);
$shopSettings->y_big = intval($_POST['y_big']);
$shopSettings->small_resize_method = intval($_POST['small_resize_method']);
$shopSettings->quantity = intval($_POST['quantity']);
$shopSettings->mapping = intval($_POST['mapping']);
$shopSettings->sorting_items = intval($_POST['sorting_items']);
$shopSettings->sub_sections = intval($_POST['sub_sections']);
$shopSettings->section_filters = intval($_POST['section_filters']);
$shopSettings->section_description = intval($_POST['section_description']);
if(isset($_POST['output_un_section'])){$shopSettings->output_un_section = intval($_POST['output_un_section']);} else{$shopSettings->output_un_section = '';}
if(isset($_POST["grouping"])){$shopSettings->grouping = intval($_POST['grouping']);} else{$shopSettings->grouping = '';}
$shopSettings->view_item_card = intval($_POST['view_item_card']);
$shopSettings->currency = $_POST['currency'];
if(isset($_POST['question'])){$shopSettings->question = intval($_POST['question']);} else{$shopSettings->question = '';}
$shopSettings->ue = intval($_POST['ue']);
$shopSettings->item_quantity = intval($_POST['item_quantity']);
$shopSettings->basket_type = intval($_POST['basket_type']);
$shopSettings->sticker_add_to_cart = trim(htmlspecialchars($_POST['sticker_add_to_cart']));

$shopSettings->yml_key = preg_replace("/[^a-z\s]/", "", $shopSettings->yml_key);

if(strlen($shopSettings->yml_key) < 3)
{
	$shopSettings->yml_key = '';

	$chars = 'abcdefghijklmnopqrstuvwxyz';
	for($i = 0; $i < 30; $i++)
		$shopSettings->yml_key .= substr($chars, rand(1, strlen($chars)) - 1, 1);
}


$s_serialize = serialize($shopSettings);

$stmt = $db->prepare("UPDATE com_shop_settings SET parametr = :s_serialize WHERE name = 'settings'");
$stmt->execute(array('s_serialize' => $s_serialize));

Header ("Location: /admin/com/shop"); exit;

?>