<?php
// загрузка файлов
defined('AUTH') or die('Restricted access');

if($filename == "import.xml" || $filename == "import0_1.xml") // ----- IMPORT -----------------------------------------
{
	$reader_xml = files_xml($filename, $file_post_data);
}
elseif($filename == "offers.xml" || $filename == "offers0_1.xml") // ----- OFFERS -----------------------------------------
{
	$reader_xml = files_xml($filename, $file_post_data);
}
else // ----- FILES -----------------------------------------
{
	$path_image = explode('/', $filename);	// это файл изображения, заносим в массив разбивку по "/"
	$ln = count($path_image); // количество элементов массива

	// пробегаемся по папкам, поэтому $i < $ln-1, а не $i <= $ln-1
	$d = '';
	for ($i = 0; $i < $ln-1; $i++)
	{
		$d .= $path_image[$i].'/';
		if(!is_dir($root.$dir.$d)){mkdir($root.$dir.$d);} // если не существует директории - создать её!
	}

	$file_img =  $root.$dir.$d.$path_image[$ln-1]; // прописываем директорию и файл, это у нас $root.$dir.$d.$path_image[$ln-1]
	file_put_contents($file_img, $file_post_data); // записываем полученный файл
}

echo "success";  // ответ 1С
exit;



// Обработка xml файлов
function files_xml($file, $file_post_data)
{
	global $root, $dir;
	
	$f_xml = fopen($root.$dir.$file, "a+");  // открываем для дозаписи, если нет - создаём его
	fwrite($f_xml, $file_post_data);  //записываем файл
	fclose($f_xml);  //закрываем файл
}

?>