<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/administrator/modules/classes/Admin.php');

$head->addFile('/modules/photo_s/admin/style.css');
$head->addFile('/modules/photo_s/frontend/style.css');
$head->addFile('/modules/photo_s/admin/tmp.js');

// id модуля
$mod_id  = intval($d[3]);
$module = new ModulesAdmin($mod_id);

function a_com()
{
	global $db, $domain, $root, $module;
	$m = $module->get_array();	

	if ($m['enabled'] == '1')
	{
		echo '
		<div class="container"><div class="photo_s_result_title">Результат:</div>';
		
		$frontend_edit = 0;
		include_once($root.'/modules/photo_s/frontend/main.php');

		echo '
			</div>
			<h1><img border="0" src="/modules/photo_s/admin/ico.png" style="float:left; margin-right:10px;" />Изображение с описанием</h1>
			<form enctype="multipart/form-data" method="POST" action="/admin/modules/photo_s/'.$m['id'].'/update">
				<table class="admin_table_2 photo_s_admin_table_2">
					<tr>
						<td class="citcle_td_1"></td>
						<td></td>
					</tr>
					<tr>
						<td>Название модуля</td>
						<td><input class="input" type="text" name="title" size="50" value="'.$m['title'].'"></td>
					</tr>
					'.$module->pub().'
					'.$module->block().'
					'.$module->order().'
					'.$module->margin().'
					<tr>
						<td>Изображение, <br>не менее 500 х 500 пикселей</td>
						<td><input type="file" name="photo" value=""></td>
					</tr>
					<tr>
						<td>Текст сверху</td>
						<td><div id="editor1"  style="width:50%" contenteditable="true">'.$m['content'].'</div></td>
					</tr>
					<tr>
						<td>Текст снизу</td>
						<td><div id="editor2" style="width:50%" contenteditable="true">'.$m['content_2'].'</div></td>
					</tr>
					'.$module->bg_color().'
					<tr>
						<td>Ссылка</td>
						<td><input class="input" type="text" name="link" size="50" value="'.$m['p1'].'"></td>
					</tr>							
				</table>
			<input id="content_1" type="hidden" name="content_1" value="">
			<input id="content_2" type="hidden" name="content_2" value="">
			
			'.$module->button().'
			
			</form>
			<script type="text/javascript">
				CKEDITOR.disableAutoInline = true;
				var photo_s_editor_1 = CKEDITOR.inline("editor1",
					{
						filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
					});
					
				var photo_s_editor_2 = CKEDITOR.inline("editor2",
					{
						filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
					});					
				// При потере фокуса
				photo_s_editor_1.on("change", function() {
					var data_1 = CKEDITOR.instances.editor1.getData();
					var mod_photo_s_text_2 = document.getElementsByClassName("mod_photo_s_text_2")[0];
					mod_photo_s_text_2.innerHTML = data_1;
				});
				
				photo_s_editor_2.on("change", function() {
					var data_2 = CKEDITOR.instances.editor2.getData();
					var mod_photo_s_text_2 = document.getElementsByClassName("mod_photo_s_text_2")[0];
					mod_photo_s_text_2.innerHTML = data_2;
				});					
				
				var button_save = document.getElementById("button_save");
				var button_apply = document.getElementById("button_apply");
				button_save.onclick = hidden;
				button_apply.onclick = hidden;				
				function hidden()
				{
					document.getElementById("content_1").value = document.getElementById("editor1").innerHTML;
					document.getElementById("content_2").value = document.getElementById("editor2").innerHTML;					
				}				
				
			</script>		
		</div>
		';		
		
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">Модуль выключен</div>';
	}
}

?>