<?php

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

/**/

const PATH_TO_ARCHIVE = '/temp/excel/photos.zip';

/**/

$TIME_MAX = ini_get('max_execution_time') - 8;
$TIME_START = time();
$INDEX = (int)$_POST['index'];

/**/

$zip = new ZipArchive();
$zip->open($_SERVER['DOCUMENT_ROOT'] . PATH_TO_ARCHIVE, ZipArchive::CREATE);

/**/

while(true)
{
	$SQL_PREPARE = $db->prepare('SELECT * FROM com_shop_item LIMIT 1 OFFSET :offset');
	$SQL_PREPARE->bindValue(':offset', (int)$INDEX, PDO::PARAM_INT);
	$SQL_PREPARE->execute();
	$item = $SQL_PREPARE->fetchAll();

	/**/

	if(count($item) === 0)
	{
		$zip->close();
		exit('-1');
	}

	/**/

	$photo_more = $item[0]['photo_more'];
	$photo_more = str_replace('.jpg', '_.jpg', $photo_more);

	$gallery = explode(';', $photo_more);
	$gallery[] = $item[0]['photo_big'];

	foreach($gallery as &$iter)
	{
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/' . $iter))
			$zip->addFile($_SERVER['DOCUMENT_ROOT'].'/components/shop/photo/' . $iter, $iter);
	}

	/**/

	$INDEX++;

	if(time() - $TIME_START >= $TIME_MAX)
	{
		echo $INDEX;
		$zip->close();
		exit();
	}

}

exit();
