<?php
// DAN 2012
// выводит содержимое раздела - центральная часть
defined('AUTH') or die('Restricted access');

echo'
	<tr>
		<td class="asm_21">&nbsp;</td>
		<td class="asm_22">
			<div class="article_sat">'.$article_title.'</div>
			<div>'.$section_article_introtext.'</div>
			'.$show_details.'
		</td>
		<td class="asm_23">&nbsp;</td>
	</tr>
	<tr>
		<td class="asm_21_sab">&nbsp;</td>
		<td class="article_sab">'.$prop.'</td>
		<td class="asm_23_sab">&nbsp;</td>
	</tr>	
';


?>