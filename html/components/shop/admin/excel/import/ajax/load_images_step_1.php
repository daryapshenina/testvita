<?php
defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

/**/

$OFFSET = (int)$_POST['offset'];
$answer = array(
	'status' => 0,
	'id' => '',
	'images' => array()
);

/* Получаем товар */

$SQL_PREPARE = $db->prepare('SELECT id, photo, photo_more FROM com_shop_item LIMIT 1 OFFSET :offset');
$SQL_PREPARE->bindValue('offset', $OFFSET, PDO::PARAM_INT);
$SQL_PREPARE->execute();
$item = $SQL_PREPARE->fetchAll();

/* Если товар получен, то получаем все имена изображений */
if(count($item) > 0)
{
	$answer['status'] = 1;
	$answer['id'] = $item[0]['id'];

	if(strlen($item[0]['photo']) > 0)
		$answer['images'][] = $item[0]['photo'];

	$arrayImages = explode(';', $item[0]['photo_more']);

	foreach($arrayImages as $iter)
	{
		if(strlen($iter) > 0)
			$answer['images'][] = $iter;
	}
}

/**/

echo json_encode($answer);
exit();
