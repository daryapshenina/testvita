<?php
// DAN 2012
// Импорт данных из 1С
defined('AUTH') or die('Restricted access');


session_start();

// не даём разорвать процесс; если процесс разорван - отправляем в начало процесса

if ($_SESSION['ses_excel_1'] != 'process_3')
{
	Header ("Location: http://".$site."/admin/com/shop/import"); exit;
}

// удаляем сессию процесса
unset($_SESSION['ses_excel_1']); 


function a_com()
{
	global $site, $root, $ext;
	
	// общее число записей c нулевой датой 
	//(нулевая дата признак того, что Excel уже загружен, но изображения ещё не обработаны)
	$item_query = mysql_query("SELECT * FROM `com_shop_item` WHERE `cdate` = '0000-00-00 00:00:00'") or die ("Невозможно сделать выборку из таблицы - 1");			
	$item_sum = mysql_num_rows($item_query);	
	
	$i = 0;

// создаём массив
	echo '
		<script language="JavaScript">
		var itemid = new Array();
		var itemphoto = new Array();		
	';	
	
	while($n = mysql_fetch_array($item_query)):
		$item_id = $n['id'];
		$item_photo = $n['photo'];	
		
		echo 'itemid['.$i.'] = '.$item_id.'; ';
		echo 'itemphoto['.$i.'] = "'.$item_photo.'"; ';
		$i++;	
	endwhile;		
	
	// создаём массив
	echo '
	</script>';		
	
	echo '
	<script language="JavaScript"> 

	
	/* ------- AJAX - загрузка ------- */
	
	function getXmlHttp()
	{
		var xmlhttp;
		try {
			xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (E) {
				xmlhttp = false;
			}
		}
		if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
	}
	

	
	// НАСТРОЙКИ ДЛЯ ПЕРВОГО ЗАПУСКА 
	itn = 0;
	its = itemid.length;
	
	
	
	function loading_ajax(itn, its, item_id, photo_send)
	{			

		document.getElementById("loading").innerHTML = "<img hspace=\"144\" src=\"http://'.$site.'/administrator/tmp/images/loading.gif\" />";
		
	
	/* --- ПРОВЕРКА ИЗОБРАЖЕНИЙ --- */
	var input_files = document.getElementById("files_field").files;	
		
	for(var i=0; i<input_files.length; i++) 
		{
			input_file = input_files[i];
			
			// имя файла
			// input_file_name = input_file.name;
			// размер
			// input_file.size;
			
			var imageType = /image.*/;  
		 	
			// проверяем на тип - должен быть тип изображения
			if (!input_file.type.match(imageType)) 
			{  
				alert ("Операция прервана. Загружаемый файл - " + input_file.name + " - не является изображением.");
				var err = 1
			} 
			
			// проверяем на размер
			if (input_file.size > 320000) 
			{  
				alert ("Операция прервана. Загружаемый файл - " + input_file.name + " - имеет размер " + input_file.size/1000 + " КБ, который превышает установленный размер в 300 КБ.");
				var err = 1
			} 			
			
		}
		/* --- / проверка изображений --- */	
		
		
		/* если загружено не изображение - прерываем загрузку */
		if (err == 1)
		{
			document.getElementById("loading").innerHTML = "<b><font color=\"#ff0000\" size=\"5\">ЗАГРУЗКА&nbsp; ПРЕРВАНА</font></b>";		
		}
		else /* --- ЗАГРУЗКА РАЗРЕШЕНА --- */
		{
			/* создание объекта формы для первоначального запуска */			
			var formData = new FormData();
	
			var req = getXmlHttp()  
			req.onreadystatechange = function() 
			{
				if (req.readyState == 4) 
				{
					if(req.status == 200) 
					{
						document.getElementById("process").innerHTML = req.responseText;			
						
						if (itn < (its))
						{
							var message_out = document.getElementById("message_out").innerHTML;
							var message_warning = document.getElementById("message_warning").innerHTML;
							document.getElementById("message_out").innerHTML = message_out + message_warning;
							
							// --- НАХОДИМ ФОТО
							var item_id = itemid[itn];
							var photo = itemphoto[itn];
							for(var i=0; i<input_files.length; i++) 
							{
								// фотография для отправки
								imagefile = input_files[i];
								
								// имя файла
								var imagefile_name = imagefile.name.toLowerCase();
								
								// если найдена фотография
								if(photo == imagefile_name)
								{
									var photo_send = imagefile;
								}
							}			
							// --- / находим фото --- 
							
							// следующий элемент
							itn = itn + 1;	
							loading_ajax(itn, its, item_id, photo_send);	
						}
						else 
						{
							document.getElementById("loading").innerHTML = "<b><font color=\"#279118\" size=\"5\">ЗАГРУЗКА&nbsp; ЗАВЕРШЕНА</font></b>";
							document.getElementById("message_loading").innerHTML = "";
							
							var message_out = document.getElementById("message_out").innerHTML;
							var message_warning = document.getElementById("message_warning").innerHTML;
							var message_title_warning = \'<div><b><font color=\"#ff0000\" size=\"3\">ВАЖНО:</font></b></div><table class="excel_tab" border="1" style="border-collapse: collapse"><tr><td width="50" class="excel_tab_hc">№</td><td width="150" class="excel_tab_hc">Артикул</td><td width="300" class="excel_tab_hc">Наименование</td><td width="200" class="excel_tab_hc">Изображение</td></tr></table>\';
							document.getElementById("message_out").innerHTML = message_title_warning + message_out + message_warning;
							
							document.getElementById("message_warning").innerHTML = "";
							document.getElementById("formsend").innerHTML = "";							
						}
	
					}
				}
			}	
			
			/* создание объекта формы */			
			var formData = new FormData();
				
			/* создание элементов формы */
			formData.append("item_id", item_id);  
			formData.append("imagefile",  photo_send);  
				
			req.open(\'POST\', \'http://'.$site.'/components/shop/admin/import_loading.php?itn=\' + itn + \'&its=\' + its, true);
			req.send(formData);
		
			// document.getElementById("process").innerHTML = "<div align=\"left\"><img src=\"http://'.$site.'/components/shop/admin/tmp/images/loading.gif\" /></div>";
		} /* --- / загрузка разрешена --- */
	}
		
	/* ------- / AJAX - загрузка ------- */	
	
	</script>		
	';		
	
	echo 
	'
	<table id="main-top-tab">
		<tr>
			<td class="imshop">Импорт данных из Excel - шаг 3 из 3</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div class="margin-left-right-10">
		<div id="formsend">
			<div>&nbsp;</div>		
			<div>&nbsp;</div>
			<div class="import_excel">Загрузка данных из Excel - загрузка изображений - <font color="#009933">шаг 3 из 3</font></div>
			<div>&nbsp;</div>	
			<div><span style="background-color: #FFFF00">Выбирайте не более <b>1000</b> изображений за один раз. Иначе загрузка будет сброшена из-за большой нагрузки на сервер.</span></div>
			<div>&nbsp;</div>			
			<div>Выберите изображения для загрузки (для выбора всех изображений из папки используйте сочетание славиш: CTRL + A)</div>
			<div>Размер каждого файла <b>не более 300 КБ.</b></div>		
			<div>&nbsp;</div>		
			<form method="post" action="" enctype="multipart/form-data">
				<table border="0" width="100%" cellpadding="0" style="border-collapse: collapse">
					<tr>
						<td width="150">Выбрать изображения:</td>
						<td><input class="import_obzor" id="files_field" type="file" id="input" multiple /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input class="import_otpravit" type="button" value="Загрузить" name="B1" onclick="loading_ajax(0,'.$item_sum.');"></td>
					</tr>
				</table>		
			</form>
		</div>
		<div>&nbsp;</div>
		<div>&nbsp;</div>		
		<div>
			<div id="loading"></div>
			<div>&nbsp;</div>			
			<div id="process"></div>
		</div>	
		<div>&nbsp;</div>	
		<div id="message_out"></div>
		<div>&nbsp;</div>			
	</div>	
	';		
}

?>
