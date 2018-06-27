<?php
// 2015
defined('AUTH') or die('Restricted access');

$data = htmlspecialchars($_POST["data"]);

$char_arr = explode('#', $data);

foreach ($char_arr as $key => $id) 
{
	$ordering = $key + 1;

	$stmt_update = $db->prepare("UPDATE com_shop_char_name SET ordering = :ordering WHERE id = :id");
	$stmt_update->execute(array('ordering' => $ordering, 'id' => $id));
}

// создаем файл в котором записываем лог
/*
$file = $root.'/components/shop/admin/chars/log.txt';
file_put_contents($file, $value);
*/

/*
$json_data = array('id'=>$img_arr[0], 'ordering'=>$img_arr[1]);
echo json_encode($json_data);
*/

exit;

?>
