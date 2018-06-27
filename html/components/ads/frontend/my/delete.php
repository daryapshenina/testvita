<?php
defined('AUTH') or die('Restricted access');

if(Auth::check())
{
	$floor_id = 1000 * floor(Auth::check()/1000); // тысячная папка
	$path = '/files/ads/'.$floor_id.'/'.Auth::check().'/';

	if(is_file($root.$path.$d[3].'.jpg')) unlink($root.$path.$d[3].'.jpg');
	if(is_file($root.$path.$d[3].'_.jpg')) unlink($root.$path.$d[3].'_.jpg');

	$stmt_delete = $db->prepare("DELETE FROM com_ads_item WHERE id = :id AND user_id = :user_id");
	$stmt_delete->execute(array('id' => $d[3], 'user_id' => Auth::check()));

	Header("Location: /ads/my");
}
else
{
	Header("Location: /ads/my");
}

?>