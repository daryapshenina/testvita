<?php
namespace Modules\JumpToTop;
defined('AUTH') or die('Restricted access');

if($modules_pub == "1")
{
	$out = '
		<div id="mod_jumptotop_main" style="width:'.$modules_p1.'px;height:'.$modules_p1.'px;left:'.$modules_p2.'px;bottom:'.$modules_p3.'px;background-color:'.$modules_p4.';opacity:0;"></div>
	';

	if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_jumptotop" data-id="">'.$out.'</div>';}
	else {echo $out;}
}

?>
