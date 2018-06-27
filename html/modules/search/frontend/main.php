<?php
namespace Modules\Search;
defined('AUTH') or die('Restricted access');

// Заголовок модуля
if ($m['titlepub'] == "1"){$title_out = '<div class="mod-title">'.$m['title'].'</div>';} else {$title_out = '';}

$out = '<div class="mod-main'.$m['module_csssuf'].'">
<div class="mod-top">'.$title_out.'</div>
	<div class="mod-mid">
		<div class="mod-padding">
		<form name="mod_form_search" method="post" action="/search">
			<div class="mod_search_main">
				<table class="mod_search_table_style">
					<tbody>
						<tr>
							<td><input class="mod_search_input" type="text" name="search" value="" autocomplete="off" placeholder="Поиск..." /></td>
							<td style="width:30px;"><div id="mod_search_submit" class="mod_search_submit" title="Найти!"></div></td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
		</div>
	</div>
	<div class="mod-bot"></div>
</div>';

// frontend редактирование
if($frontend_edit == 1){echo '<div class="edit_mode" data-type="mod_search" data-id="'.$m['id'].'">'.$out.'</div>';}
else {echo $out;}

?>
