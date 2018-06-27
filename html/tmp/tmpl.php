<?php
defined("AUTH") or die("Restricted access");
if($d[0] == '')
{
	include('tmpl-main.php');
}
else
{
	include('tmpl-def.php');
}
?>
