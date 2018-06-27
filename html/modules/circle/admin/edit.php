<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/administrator/modules/classes/Admin.php');

$head->addFile('/modules/circle/admin/style.css');
$head->addFile('/modules/circle/frontend/style.css');
$head->addFile('/modules/circle/admin/tmp.js');

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
		<div class="container">
			<h1><img border="0" src="/modules/circle/admin/ico.png" style="float:left; margin-right:10px;" />Круг с анимацией.</h1>
			<form enctype="multipart/form-data" method="POST" action="/admin/modules/circle/'.$m['id'].'/update">
			
			<table class="admin_table_2">
				<tr>
					<td class="circle_td">
						<table class="admin_table_2">
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
							'.$module->padding().'
							'.$module->width_fix($m['width_f']).'
							'.$module->height_fix($m['height_f']).'
							'.$module->effect_a($m['effect_a']).'	
							<tr>
								<td>Текст при наведении</td>
								<td><input class="input" type="text" name="content" value="'.$m['content'].'"></td>
							</tr>
							<tr>
								<td>Цвет текста</td>
								<td><div><input id="text_color" type="color" name="text_color" value="'.$m['color'].'" autocomplete="on"></div></td>
							</tr>
							'.$module->bg_color().'
							<tr>
								<td>Размер шрифта</td>
								<td><input id="font_size_input" type="range" min="10" max="36" name="font_size" value="'.$m['p1'].'"><span id="font_size_out" style="font-size:32px; margin-left:20px;"></span></td>
							</tr>
							<tr>
								<td>Изображение фона</td>
								<td><input type="file" name="photo" value=""></td>
							</tr>
							<tr>
								<td>Текст снизу<br><span class="circle_help">Если текст не умещается полностью - увеличьте высоту модуля</span></td>
								<td><div id="editor1" style="width:'.$m['width_f'].'px;" contenteditable="true">'.$m['content_2'].'</div></td>
							</tr>
							<tr>
								<td>Ссылка</td>
								<td><input class="input" type="text" name="link" size="50" value="'.$m['p2'].'"></td>
							</tr>							
						</table>
					</td>
					<td class="circle_td_sep"></td>						
					<td class="circle_td">
					<div class="circle_result_title">Результат:</div>
					<div class="circle_result_container">
					';

					$frontend_edit = 0;
					include_once($root.'/modules/circle/frontend/main.php');

					echo '
					</div>
					</td>
				</tr>
			</table>
			<input id="text" type="hidden" name="text" value="">
	
			'.$module->button().'			
			</form>
			<script type="text/javascript">
				CKEDITOR.disableAutoInline = true;
				var circle_editor = CKEDITOR.inline("editor1",
					{
						filebrowserBrowseUrl : \'/administrator/plugins/browser/dan_browser.php\',
					});
				// При потере фокуса
				circle_editor.on("change", function() {
					var data = CKEDITOR.instances.editor1.getData();
					var mod_circle_text_2 = document.getElementsByClassName("mod_circle_text_2")[0];
					mod_circle_text_2.innerHTML = data;
				});	
				
				var button_save = document.getElementById("button_save");
				var button_apply = document.getElementById("button_apply");
				button_save.onclick = hidden;
				button_apply.onclick = hidden;				
				function hidden()
				{
					document.getElementById("text").value = document.getElementById("editor1").innerHTML;					
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