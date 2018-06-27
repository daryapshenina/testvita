<?php
// DAN разработка - январь 2014
defined('AUTH') or die('Restricted access'); 

$id = intval($_POST['id']);

// $file = $root.'/components/shop/admin/chars/log_delete.txt';
// file_put_contents($file, $id);

$stmt_name = $db->prepare("DELETE FROM com_shop_char_name WHERE id = :id");
$stmt_name->execute(array('id'=>$id));

$stmt_char = $db->prepare("DELETE FROM com_shop_char WHERE name_id = :name_id");
$stmt_char->execute(array('name_id'=>$id));

exit;

?>
