<?php
// DAN 2010
defined('AUTH') or die('Restricted access');

$_SESSION['ses_excel_1'] = 'process_1';


function a_com()
{ 
	global $site, $task; 
		
	echo 
	'
	<script language="JavaScript">
	function it(t) 
	{	
		if (t == 1)
		{
			document.getElementById("import_type_1").style.background = \'#C8FF99\';			
			document.getElementById("import_type_2").style.background = \'none\';
			document.getElementById("import_type_1_set").innerHTML = \'<div><input id="old_delete" class="shop-rb-checked" type="checkbox" name="old_delete" value="1" checked /><label class="shop-rb" for="old_delete">Удалить старые записи (старые товары и разделы будут удалены)</label></div>\';			
			document.getElementById("import_type_2_set").innerHTML = \'\';			
		}
		if (t == 2)
		{
			document.getElementById("import_type_2").style.background = \'#C8FF99\';			
			document.getElementById("import_type_1").style.background = \'none\';
			document.getElementById("import_type_1_set").innerHTML = \'\';	
			document.getElementById("import_type_2_set").innerHTML = \'<div><input id="import_type_2_set_intro" class="shop-rb" type="checkbox" name="intro_text" value="1"/><label class="shop-rb" for="import_type_2_set_intro">Вводный текст</label></div><div><input id="import_type_2_set_fulltext" class="shop-rb" type="checkbox" name="full_text" value="1"/><label class="shop-rb" for="import_type_2_set_fulltext">Детальное описание</label></div><div><input id="import_type_2_set_imades" class="shop-rb" type="checkbox" name="image" value="1"/><label class="shop-rb" for="import_type_2_set_imades">Изображения - загрузить / обновить изображения (процедура занимает много времени)</label></div>\';			
		}			
	}
	</script>
				
	
	<table id="main-top-tab">
		<tr>
			<td class="imshop">Импорт данных из Excel - шаг 1 из 3</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">
		<div>&nbsp;</div>
		<div class="import_excel">Загрузка данных из Excel - <font color="#009933">шаг 1 из 3</font></div>
		<div>&nbsp;</div>	
		<div><span style="background-color: #FFFF00">Загружайте не более <b>1000</b> позиций товаров за один раз. Иначе загрузка будет сброшена из-за большой нагрузки на сервер.</span></div>
		<div>&nbsp;</div>
		<form method="POST" enctype="multipart/form-data" action="http://'.$site.'/admin/com/shop/import_and_export/import_excel_v">
		<div id="import_type_1">
			<input id="imt_1" class="shop-rb" type="radio" value="1" name="import_type" onClick="it(1);" />
			<label class="shop-rb" for="imt_1"><b>Новая загрузка</b> (создаёт новые разделы и товары)</label>
			<div id="import_type_1_set"></div>			
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>		
		<div id="import_type_2">
			<input id="imt_2" class="shop-rb" type="radio" checked  value="2" name="import_type" onClick="it(2);" />
			<label class="shop-rb" for="imt_2"><b>Обновление</b> (не затрагивает структуру разделов) наименование, цена, действия - обновляются по умолчанию </label>		
			<div id="import_type_2_set"></div>
		</div>
		<div>&nbsp;</div>		
		<div><input class="import_obzor" type="file" name="price" size="30"></div>
		<div><input class="import_otpravit" type="submit" value="Отправить" name="send"></div>
		</form>
		<div>&nbsp;</div>		
		<div><b>Размер файла не должен превышать 10 Мб.</b></div>		
		<div>&nbsp;</div>		
		<div>Прайс-лист должен быть сохранён с разрешение "<font color="#009933"><b>xls</b></font>" или "<font color="#009933"><b>xlsx</b></font>"</div>	
	</div>	
	<script language="JavaScript">it(2);</script>	
	';	


} // конец функции
