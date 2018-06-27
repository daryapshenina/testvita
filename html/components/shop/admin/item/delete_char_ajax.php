<?php
// DAN разработка - январь 2014
defined('AUTH') or die('Restricted access'); 

$id = intval($_POST['id']);

$stmt_delete = $db->prepare('DELETE FROM com_shop_char WHERE id = :id LIMIT 1');
$stmt_delete->execute(array('id' => $id));

exit;

?>
