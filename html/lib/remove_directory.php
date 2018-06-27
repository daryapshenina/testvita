<?php
defined("AUTH") or die("Restricted access");

// Рекурсивное удаление директории
function remove_directory($_dir) 
{
	if ($objs = glob($_dir."/*")) 
	{
		foreach($objs as $obj)
		{
     		is_dir($obj) ? remove_directory($obj) : unlink($obj);
   		}
	}
	rmdir($_dir);
}

?>