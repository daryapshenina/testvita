<?php
defined('AUTH') or die('Restricted access');

$id = $_POST["id"];
$type = $_POST["type"];
$data = $_POST["data"];

/*
// ******* ПРОВЕРКА ********
$str = "----------------- \n";
$str .= 'id - '.$id."\n";
$str .= 'type - '.$type."\n";
$str .= 'data - '.$data."\n";
$str .= "   \n";

$file_out = $root.'/update.log';
$f = fopen($file_out,"a+");
fwrite($f,$str);
fclose($f);
// ******** / проверка ********	
*/

if($type == 'item_title')
{
	$stmt = $db->prepare("UPDATE com_shop_item SET title = :data WHERE id = :id");
	$stmt->execute(array('data' => $data, 'id' => $id));		
}

if($type == 'item_price' || $type == 'item_price_discount')
{
	$replace = array(' ', '&nbsp;');
	$data = intval(str_replace($replace, '', $data));
	$stmt = $db->prepare("UPDATE com_shop_item SET price = :data WHERE id = :id");
	$stmt->execute(array('data' => $data, 'id' => $id));		
}

if($type == 'item_price_old')
{
	$replace = array(' ', '&nbsp;');
	$data = intval(str_replace($replace, '', $data));
	$stmt = $db->prepare("UPDATE com_shop_item SET price_old = :data WHERE id = :id");
	$stmt->execute(array('data' => $data, 'id' => $id));		
}

if($type == 'item_intro_text')
{
	$stmt = $db->prepare("UPDATE com_shop_item SET intro_text = :data WHERE id = :id");
	$stmt->execute(array('data' => $data, 'id' => $id));		
}

if($type == 'item_full_text')
{
	$stmt = $db->prepare("UPDATE com_shop_item SET full_text = :data WHERE id = :id");
	$stmt->execute(array('data' => $data, 'id' => $id));		
}

echo 'ok';
exit;
?>