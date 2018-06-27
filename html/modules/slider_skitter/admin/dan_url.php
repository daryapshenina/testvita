<?php
// DAN 2010
// defined('AUTH') or die('Restricted access');

// Файл вставляет данные в редактор.
// Фильтруем данные
$str = $_GET["url"];
$funcNum = $_GET["fn"];;
include("../../../config.php");
include("../../../lib/lib.php");
$url = zapros($str);

Header ("Location: http://".$site."/admin/modules/slider_skitter"); exit;

?>