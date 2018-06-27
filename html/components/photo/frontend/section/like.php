<?php
defined('AUTH') or die('Restricted access');

$id = intval($d[3]);

$stmt_update = $db->prepare('UPDATE com_photo_items SET likes = likes + 1 WHERE id = :id');
$stmt_update->execute(array('id' => $id));


exit;
?>