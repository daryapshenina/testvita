<?php
defined('AUTH') or die('Restricted access');

$id = intval($_POST['id']);

$stmt = $db->prepare('DELETE FROM com_shop_related_item WHERE id = :id');
$stmt->execute(array('id' => $id));

exit;
?>