<?php
// DAN 2012
// выводит заголовок, описание и фильтры раздела
defined('AUTH') or die('Restricted access');

echo
'
<table class="article_section_maintab">
'.$section_display_sorting_tmp.'
	<tr>
		<td class="asm_21">&nbsp;</td>
		<td class="asm_22">
			<div>&nbsp;</div>		
			<div class="title">
				<div class="title-1"></div>
				<div class="title-2"><h1>'.$section_title.'</h1></div>
				<div class="title-3"></div>			
			</div>
			<div>&nbsp;</div>
			'.$section_description.'
		</td>
		<td class="asm_23">&nbsp;</td>
	</tr>			
';

?>