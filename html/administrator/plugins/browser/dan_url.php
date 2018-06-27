<?php
// Файл вставляет данные в редактор.
// Фильтруем данные
$str = $_GET["url"];
$funcNum = $_GET["fn"];;
include("../../../lib/lib.php");
$url = zapros($str);

$url = "/$url";
$message=""; // Сообщение об ошибке
echo "
	<script type='text/javascript'>
		window.opener.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');
		window.close();
	</script>
";
echo $url; 

?>