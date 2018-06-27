<?php
defined('AUTH') or die('Restricted access');

/**/

const PATH_TO_ARCHIVE = '/temp/excel/photos.zip';

if(is_file($_SERVER['DOCUMENT_ROOT'] . PATH_TO_ARCHIVE))
	@unlink($_SERVER['DOCUMENT_ROOT'] . PATH_TO_ARCHIVE);

/**/

$numberImages = 0;

/**/

$SQL_PREPARE = $db->query('SELECT photo_big, photo_more FROM com_shop_item');
$items = $SQL_PREPARE->fetchAll();

foreach($items as $item)
{
	if(strlen($item['photo_big']) > 0)
		$numberImages++;

	$photo_more = $item['photo_more'];
	$photo_more = explode(';', $photo_more);
	$numberImages += count($photo_more);
}

echo $numberImages;
exit();
