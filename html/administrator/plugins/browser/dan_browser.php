<?php
// DAN 2014
define("AUTH", TRUE);

session_start();
include("../../../config.php");
include("../../../lib/lib.php");

// увы, но на время перехода на pdo - будем использовать 2 драйвера... однако весело!!!
$db_host = $host;
$db_name = $dbname;
$db_user = $user;
$db_password = $passwd;

$db_dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$db_opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"	
);
$db = new PDO($db_dsn, $db_user, $db_password, $db_opt);

include("../../../administrator/login.php");

$root=$_SERVER['DOCUMENT_ROOT'];
$dir = "$root/files/"; // Путь до пользовательских файлов

// ======= Получаем данные, фильтруем ============================================================
if(isset($_GET["act"])){$act = checkingeditor($_GET["act"]);} else{$act = '';}
if(isset($_REQUEST["ex"])){$ex = checkingeditor($_REQUEST["ex"]);} else{$ex = '';}
if(isset($_POST["filename_new"])){$filename_new = checkingeditor($_POST["filename_new"]);} else{$filename_new = '';}
if(isset($_REQUEST["filename_old"])){$filename_old = checkingeditor($_REQUEST["filename_old"]);} else{$filename_old = '';}
if(isset($_REQUEST["dir_current"])){$dir_current = checkingeditor($_REQUEST["dir_current"]);} else{$dir_current = '';} // текущая директория
if(isset($_GET["CKEditorFuncNum"])){$fn = intval($_GET["CKEditorFuncNum"]);} else {$fn = '';}

if(isset($_FILES['filename_new']))
{
	// Данные загрузки файла
	$file_name = checkingeditor_2($_FILES['filename_new']['name']); // Оригинальное имя файла на компьютере клиента. 
	$file_type = checkingeditor_2($_FILES['filename_new']['type']); // Mime-тип файла, в случае, если браузер предоставил такую информацию. Пример: "image/gif". 
	$file_size = checkingeditor_2($_FILES['filename_new']['size']); // Размер в байтах принятого файла. 
	$tmp_name = $_FILES['filename_new']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере. 
}

$filename_new_len = strlen($filename_new); 	
if ($filename_new_len > 50){$filename_new = substr($filename_new, 0, 50);}

// ======= Дериктории ===============================================================================
if (!isset($dir_current) || $dir_current == "") 
{
	$dir_current = ""; // Текущая директория
	$topfolder = ""; // "Вверх" - родительская папка
}
else
{
	$lastchr = strrpos($dir_current, '/'); // Находит последнюю позицию символа '/' в строке
	$topfolderdir = substr($dir_current, 0, $lastchr);  // Директория родительской папки		
	$topfoldername = substr($dir_current, $lastchr);   // Наименование родительской папки
	$topfoldername = str_replace('/', '', $topfoldername);
	$dir_current = $dir_current.'/';
	$dir_current = str_replace('//', '/', $dir_current);
	
	$topfolder = '
	<div class="brw_block">
		<div class="brw_im"><a class="ico_link" href="dan_browser.php?dir_current='.$topfolderdir.'&CKEditorFuncNum='.$fn.'"><img border="0" src="images/topfolder.png"  style="margin-top:25px; height:50px; width:50px; border:0px;"></a></div>
		<div class="brw_txt"><a class="ico_link_top" href="dan_browser.php?dir_current='.$topfolderdir.'&CKEditorFuncNum='.$fn.'"><b>'.$topfoldername.'</b></a></div>
	</div>	
	';
}

$alert = '';

// ####### УСЛОВИЯ ###################################################################################
// ======= Переименовать файл ========================================================================

if ($act == "3")
{

	// Проверяем запрещённые расширения; проверка двойного расширения; проверка на длинну расширения 
	if((preg_match("/(\.exe)|(\.php)|(\.cgi)|(\.pl)|(\.phtml)|(\.html)/", $filename_new)) || (preg_match("/(exe)|(php)|(cgi)|(pl)|(phtml)|(html)/", $ex))) // Проверка 
	{
		die("Файлы такого типа запрещено загружать на сервер - 1!");
	}

	if ($ex == '' || !isset ($ex)) // Нет расширения - признак папки - переименовываем папку
	{
		$oldnamefolder = $dir.$dir_current.$filename_old;		
		$newnamefolder = $dir.$dir_current.$filename_new;
		
		if(file_exists($newnamefolder))  
		{  
			 if(is_dir($newnamefolder)) 
			 { 
			 	$alert = '<script type="text/javascript">alert("Папка с таким именем уже существует!");</script>';  
			 } 
		}
		else 
		{
			$newnamefolder = strtolower($newnamefolder);
			rename ($oldnamefolder, $newnamefolder);	
		}			
	}
	else  // если есть расширение - значит файл
	{
		if(strlen($ex) > 4 || (!preg_match("/(jpg)|(jpeg)|(gif)|(png)|(doc)|(docx)|(rtf)|(xls)|(xlsx)|(rar)|(zip)|(avi)|(mpg)|(mpeg)|(swf)|(pdf)/", $ex))) // Проверка 
		{
			die("Файлы такого типа запрещено загружать на сервер - 2!");
		}

		$oldnamefile = $dir.$dir_current.$filename_old.'.'.$ex;
		$newnamefile = $dir.$dir_current.$filename_new.'.'.$ex;	
		if (file_exists($newnamefile)) // проверяем, существует ли файл с таким именем
		{ 
			// выводимое сообщение
			$alert = '<script type="text/javascript">alert("Файл с таким именем уже существует!");</script>';
		}
		else 
		{
			$newnamefile = strtolower($newnamefile);
			rename ("$oldnamefile", "$newnamefile");	
		}
	} 
}

// === Удалить папку или файл =====================================================================================

if ($act == "4")
{
	if ($ex == '' || !isset ($ex)) // Нет расширения - признак папки - удаляем папку
	{
		if(file_exists($dir.$dir_current.$filename_old))
		{
			removedir($dir.$dir_current.$filename_old);
		}					
	}
	else  // если есть расширение - значит файл - удаляем файл
	{
		if(file_exists($dir.$dir_current.$filename_old.".".$ex))
		{
			unlink($dir.$dir_current.$filename_old.".".$ex);
		}		
	} 

}
	
// ======= Создать новую папку =========================================================================

if ($act == "5")
{
	if(file_exists($dir.$dir_current.$filename_new))  
	{  
		if(is_dir($dir.$dir_current.$filename_new)) 
		{ 
			$alert = '<script type="text/javascript">alert("Папка с таким именем уже существует!");</script>';  
		} 
	}
	else 
	{
		$alert = "";
		$directory = strtolower($dir.$dir_current.$filename_new); // переводим в нижний регистр
		mkdir($directory, 0755);	
	}		
}
	
// ======= Загрузить файл ===================================================================================

if ($act == "6")
{ 
	if ($file_size >= 5000000) // Проверка размера файла
	{
		$alert = '<script type="text/javascript">alert("Файл слишком большой! Максимальный размер файла не более 5Mb.");</script>';
	}
	else
	{	
		if(!isset($file_name)|| $file_name=="" ) // Проверка того, что файл выбран (поле обзор не пустое)
		{
			$alert = '<script type="text/javascript">alert("Файл не выбран или файл слишком большой!");</script>';
		}
		else 
		{	
			// проверка на двойное разрешение 'test.php.zip'
			// разбиваем файл (оригинальное имя на компьютере клиента) на части по признаку '.' точка
			$file_name_arr = preg_split('/\./', $file_name, -1 , PREG_SPLIT_NO_EMPTY); 
			$count_tmp_arr = sizeof($file_name_arr); // размер массива; при значении > 2 означает, что у файла двойное расширение 
			// проверка на длинну расширения, находим длинну расширения
			$file_name_len = strlen ($file_name_arr[1]);
			// Проверяем запрещённые расширения; проверка двойного расширения; проверка на длинну расширения 
			if((preg_match("/(\.exe)|(\.php)|(\.cgi)|(\.pl)|(\.phtml)/", $file_name)) || $count_tmp_arr > 2 || $file_name_len > 4) // Проверка 
			{
				@chmod($tmp_name,0755);
				unlink($tmp_name);
				$alert = '<script type="text/javascript">alert("Файлы такого типа запрещено загружать на сервер!");</script>';		
			}
			else
			{
				// Проверяем разрешённые расширения
				$file_name = strtolower($file_name); // переводим в нижний регистр
				
				if(preg_match("/(\.jpg)|(\.jpeg)|(\.gif)|(\.png)|(\.doc)|(\.docx)|(\.rtf)|(\.xls)|(\.xlsx)|(\.rar)|(\.zip)|(\.avi)|(\.mpg)|(\.mpeg)|(\.swf)|(\.pdf)/", $file_name)) // Проверка 
				{
					$alert = "";
					$ln_file_name = strlen($file_name); // Количество символов в именим файла
					if ($ln_file_name > 50) // Обрезаем длинну
					{
						$file_nm_1 = substr($file_name, 0, 54);
						$file_name = $file_nm_1.'.'.strtolower($file_name_arr[1]); // приклеиваем разрешение с переводом в нижний регистр
					}
				//	$file_name=substr($file_name, 0, 10);
					move_uploaded_file($tmp_name, $dir.$dir_current.$file_name); // Копируем временный файл в указанную директорию с указанным именем	
					@chmod($dir.$dir_current.$file_name,0644);
				}		
				
				else // удаляем временный файл
				{
					@chmod($tmp_name,0755);
					unlink($tmp_name);
					$alert = '<script type="text/javascript">alert("Файлы такого типа запрещено загружать на сервер!");</script>';
				}
			}
		}
	} // конец проверки размера файла
}



################################################################################################
// ======= Вывод содержимого ===================================================================

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
<title>DAN - browser</title> 
<link rel="stylesheet" href="dan_browser_style.css" type="text/css" />
<link rel="stylesheet" href="http://<? echo $site; ?>/js/dan.framework.css" type="text/css" />
<script src="http://<? echo $site; ?>/js/dan.framework.js" type="text/javascript"></script>
<script type="text/javascript">

function rename(ico_name, extn){

	if (extn == '' || extn == undefined) // папка
	{
		name_out = ico_name;
		extn = '';
	}
	else
	{
		name_out = ico_name + '.' + extn;
	}
	
	// --- выводим картинку в зависимости от расширения файла --- 
	// Папка 
	if (extn == ''){danout='<img border="0" src="images/folder.png" width="50" height="50" align="middle">';}
	// Изображение
	if (extn == 'jpg'||extn == 'jpeg'||extn == 'gif'||extn == 'png'){ danout = '<img border="0" src="<? echo'/files/'.$dir_current; ?>'+ico_name+'.'+extn+'" width="100" height="100">';}
	// Документ Word 
	if (extn == 'doc'||extn == 'docx'||extn == 'rtf'){ danout='<img border="0" src="images/doc.gif" width="50" height="50" align="middle">';}
	// Excel
	if (extn == 'xls'||extn == 'xlsx'){ danout = '<img border="0" src="images/excel.gif" width="50" height="50" align="middle">';}
	// Архив 
	if (extn == 'rar'||extn == 'zip'){ danout = '<img border="0" src="images/rar.gif" width="50" height="50" align="middle">';}	
	// Видео 
	if (extn == 'avi'||extn =='mpg'||extn == 'mpeg'){ danout='<img border="0" src="images/video.gif" width="50" height="50" align="middle">';}	
	// Flash 
	if (extn == 'swf'){ danout = '<img border="0" src="images/flash.gif" width="50" height="50" align="middle">';}
	// PDF 
	if (extn == 'pdf'){ danout = '<img border="0" src="images/pdf.gif" width="50" height="50" align="middle">';}	

	out = '<div class="send_title">Переименовать <br/><br/></div>';
	out += '<div class="dialogue">&nbsp;</div>';
	out += '<div class="dialogue"><span id="filenorename"><a href="dan_browser.php?dir_current=<? echo $dir_current ?>&amp;CKEditorFuncNum=2"><img border="0" src="images/nodelete.png" width="50" height="50" align="middle" hspace="10">Не переименовывать</a></span></div>';
	out += '<div>&nbsp;</div>';
	out += '<div>&nbsp;</div>';	
	out += '<div class="text-center">'+danout+'</div>';
	out += '<div>&nbsp;</div>';
	out += '<div class="text-center">'+name_out+'</div>';	
	out += '<div>&nbsp;</div>';	
	out += '<hr/>';
	out += '<div class="dialogue_image"><span id="filerename">Старое название файла: <b>'+name_out+'</b><br><br>';
	out += '<form method="POST" action="dan_browser.php?act=3&amp;dir_current=<? echo $dir_current ?>&amp;CKEditorFuncNum=2">';
	out += '<p><input value="'+ico_name+'" type="text" name="filename_new" size="30"><input value="'+ico_name+'" type="hidden" name="filename_old"><input value="'+extn+'" type="hidden" name="ex"><input type="submit" value="Переименовать" name="bt"></p>';
	out += '</form></span></div>';

	document.getElementById("dan_framework_body_lightbox_0").innerHTML = out; 
}


function del(ico_name, extn){

	if (extn == '' || extn == undefined) // папка
	{
		extn = '';
	}

	// --- выводим картинку в зависимости от расширения файла --- 
	// Папка 
	if (extn == ''){ danout='<img border="0" src="images/folder.png" width="50" height="50" align="middle">'; }
	// Изображение
	if (extn=='jpg'||extn=='jpeg'||extn=='gif'||extn=='png'){ danout='<img border="0" src="<? echo'/files/'.$dir_current; ?>'+ico_name+'.'+extn+'" width="100" height="100">';}
	// Документ Word 
	if (extn=='doc'||extn=='docx'||extn=='rtf'){ danout='<img border="0" src="images/doc.gif" width="50" height="50" align="middle">';}
	// Excel
	if (extn=='xls'||extn=='xlsx'){ danout='<img border="0" src="images/excel.gif" width="50" height="50" align="middle">';}
	// Архив 
	if (extn=='rar'||extn=='zip'){ danout='<img border="0" src="images/rar.gif" width="50" height="50" align="middle">';}	
	// Видео 
	if (extn=='avi'||extn=='mpg'||extn=='mpeg'){ danout='<img border="0" src="images/video.gif" width="50" height="50" align="middle">';}	
	// Flash 
	if (extn=='swf'){ danout='<img border="0" src="images/flash.gif" width="50" height="50" align="middle">';}
	// PDF 
	if (extn=='pdf'){ danout='<img border="0" src="images/pdf.gif" width="50" height="50" align="middle">';}	

	out = '<div class="send_title">Удалить <br/><br/></div>';
	out += '<div class="dialogue"><span id="filedelete"><a href="dan_browser.php?act=4&amp;dir_current=<? echo $dir_current ?>&amp;filename_old='+ico_name+'&amp;ex='+extn+'&amp;CKEditorFuncNum=2"><img border="0" src="images/delete.png" width="50" height="50" align="middle" hspace="10">Удалить</a></span></div>';
	out += '<div class="dialogue"><span id="filenodelete"><a href="dan_browser.php?dir_current=<? echo $dir_current ?>&amp;CKEditorFuncNum=2"><img border="0" src="images/nodelete.png" width="50" height="50" align="middle" hspace="10">Не удалять</a></span></div>';
	out += '<div>&nbsp;</div>';
	out += '<hr/>';
	out += '<div>&nbsp;</div>';	
	out += '<div class="text-center">'+danout+'</div>';
	out += '<div>&nbsp;</div>';
	out += '<div class="text-center">'+ico_name+'.'+extn+'</div>';
	out += '<div>&nbsp;</div>';	

	document.getElementById("dan_framework_body_lightbox_0").innerHTML = out; 
}


function DAN_contextmenu(height, width, name, content){
	var item_link = document.getElementsByName(name);

    for (var i = 0; i < item_link.length; i++)
	{
		item_link[i].oncontextmenu = function(e){
		
			// url адрес ссылки разбиваем на '='
			str_arr = this.href.split('=');
			m = str_arr[1].split('&');
			file_selected = m[0];
			
			file_selected_arr = file_selected.split('/');
			file_name = file_selected_arr[(file_selected_arr.length - 1)];	
			file_name_arr = file_name.split('.');
			
			ico_name = file_name_arr[0];		
			extn = file_name_arr[1];
			
			if (extn == undefined){extn = '';}
			
			// сначала разбиваем на массив, без учёта последнего разбиения - и склеиваем - получаем текущую директорию
			// dir_current = file_selected.split('/', (file_selected_arr.length - 1)).join('/');	
			

			// защита от повторного контекстного окна
			if (document.getElementById('contextmenu') == null)
			{
				// отмена действий браузера - кроссбраузерный код
				event = e || window.e;
				
				var c_menu = document.createElement('div');
				c_menu.id = 'contextmenu';
				
				var body_child_0 = document.body.children[0];
				document.body.insertBefore(c_menu, body_child_0);
			
				c_menu.style.top = (event.pageY - 10) + 'px';
				c_menu.style.left = (event.pageX - 10) +'px';
				
				c_menu.innerHTML = '<a onclick="DAN_modal(400, 600, \'\', \'\'); rename(\''+ ico_name +'\',\''+ extn +'\');" class="contextmenu_str"><img border="0" src="images/rename_file.gif">Переименовать</a><a onclick="DAN_modal(300, 600, \'\', \'\'); del(\''+ ico_name +'\',\''+ extn +'\');" class="contextmenu_str"><img border="0" src="images/delete_file.gif">Удалить</a>';
				
				if (event.preventDefault) {  // если метод существует
					event.preventDefault();
				} 
				else { // вариант IE<9:
					event.returnValue = false;
				}
			}
		}		
	}
	
	// удаляем контекстное меню
	document.body.onclick = function() 
	{	
		node = document.getElementById('contextmenu');
		if(node)
		{
			document.body.removeChild(node);
		}
	}		
	
}

</script>
</head>
<body>
<? echo $alert; ?>
<table class="brw-main-tab">
	<tr>
		<td class="brw-bottom" colspan="3">
        	<br/>
        	<div id="newfolder">
            <img border="0" src="images/newfolder.png" width="50" height="50" align="left" hspace="10">
            	Создать новую папку <br/>
                <form method="POST" action="dan_browser.php?act=5&dir_current=<? echo $dir_current; ?>&CKEditorFuncNum=<? echo"$fn"; ?>">
                    <input type="text" name="filename_new" size="20"><input type="submit" value="Создать" name="bf">
                </form>            
            </div>
         	<div id="newfile">
            <img border="0" src="images/download.png" width="50" height="50" align="left" hspace="10">
            	Загрузить новый файл. &nbsp;&nbsp;<font size="2">Размер файла до 2Mb.</font><br/>
                <form method="POST" enctype="multipart/form-data" action="dan_browser.php?act=6&dir_current=<? echo $dir_current; ?>&CKEditorFuncNum=<? echo"$fn"; ?>">
                    <input type="file" name="filename_new" size="30">&nbsp;<input type="submit" value="Загрузить" name="B1">
                </form>            
            </div>           
        </td>
	</tr>
	<tr>
		<td id="brw-folder"><?php getListFolder('', 0); ?></td>
		<td id="brw-sep"></td>
		<td id="brw-center">
<? 
// ======= Выводим все папки и файлы ==============================================================
// Выводим текущую папку
echo $topfolder;

if ($handle = opendir($dir.$dir_current)) // путь до корня и текущий
{
	$file_arr = scandir($dir.$dir_current);

	foreach($file_arr as $file)
	{
    	if ($file != '.' && $file != '..')
		{				
			// Разбиваем на расширения	
			$l_file = strtolower($file); // переводим в нижний регистр
			$file_arr = preg_split('/\./', $l_file, -1 , PREG_SPLIT_NO_EMPTY); 
			$name = $file_arr[0]; // имя файла.
			if(isset($file_arr[1])){$ext = $file_arr[1];} else{$ext = '';} // расширение.
			// Находим папку и переносим в массив
			if ($ext == "" ){ $folder[] = $name; }
			if ($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "png" ){ $images[]=$name; $images_extn[]=$ext; }
			if ($ext == "doc" || $ext == "docx" || $ext == "rtf" ){ $doc[] = $name; $doc_extn[] = $ext;}
			if ($ext == "xls" || $ext == "xlsx" ){ $excel[] = $name; $excel_extn[] = $ext;}
			if ($ext == "rar" || $ext == "zip"){ $rar[] = $name; $rar_extn[] = $ext;}
			if ($ext == "avi" || $ext == "mpg" || $ext == "mpeg" ){ $video[] = $name; $video_extn[] = $ext;}		
			if ($ext == "swf" ){ $flash[] = $name; $flash_extn[] = $ext;}	
			if ($ext == "pdf" ){ $pdf[] = $name; $pdf_extn[] = $ext;}				
		}
	}
    closedir($handle);
}

if(isset($folder))
{
	// Выводим массив папок	
	for ($i = 1; $i <= count($folder); $i++) 
	{ 
		$j = $i-1;
		$extn = "";  
		$ico_im = '<img src="images/folder.png"  style="margin-top:25px; height:50px; width:50px; border:0px;">';
		$ico_name = $folder[$j];
		fileico($ico_im, $ico_name, $extn, '');
	}
}

if(isset($images))
{
	// Выводим массив изображений	
	for ($i = 1; $i <= count($images); $i++) 
	{ 
	   $j = $i-1;
		$extn = $images_extn[$j];  
		$ico_im = '<img src="/files/'.$dir_current.$images[$j].'.'.$extn.'" style="height:100px; width:100px; border:0px;">';
		
		$im_arr = getimagesize($dir.$dir_current.$images[$j].'.'.$extn);

		$file_char = $im_arr[0].' х '.$im_arr[1].'px<br>';
		$file_char .= intval((filesize($dir.$dir_current.$images[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$images[$j].'.'.$extn));		
		
		$ico_name = $images[$j];
		fileico($ico_im, $ico_name, $extn, $file_char);
	}
}

if(isset($doc))
{
	// Выводим массив Word	
	for ($i = 1; $i <= count($doc); $i++) 
	{ 
		 $j = $i-1;
		$extn = $doc_extn[$j];  
		$ico_im = '<img src="images/doc.gif"  style="margin-top:25px; height:50px; width:50px; border:0px;">';
		
		$file_char = intval((filesize($dir.$dir_current.$doc[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$doc[$j].'.'.$extn));		
		
		$ico_name = $doc[$j];
		fileico($ico_im, $ico_name, $extn, $file_char);
	}
}

if(isset($excel))
{
	// Выводим массив Excel	
	for ($i = 1; $i <= count($excel); $i++) 
	{ 
		$j = $i-1;
		$extn = $excel_extn[$j];  
		$ico_im = '<img src="images/excel.gif"  style="margin-top:25px; height:50px; width:50px; border:0px;">';
		
		$file_char = intval((filesize($dir.$dir_current.$excel[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$excel[$j].'.'.$extn));
		
		$ico_name = $excel[$j];
		fileico($ico_im, $ico_name, $extn, $file_char);
	}
}

if(isset($rar))
{
	// Выводим массив Архив	
	for ($i = 1; $i <= count($rar); $i++) 
	{ 
		$j = $i-1;
		$extn = $rar_extn[$j];  
		$ico_im = '<img src="images/rar.gif"  style="margin-top:25px; height:50px; width:50px; border:0px;">';
		
		$file_char = intval((filesize($dir.$dir_current.$rar[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$rar[$j].'.'.$extn));
		
		$ico_name = $rar[$j];
		fileico($ico_im, $ico_name, $extn, $file_char);
	} 
}

if(isset($video))
{
	// Выводим массив Видео	
	for ($i = 1; $i <= count($video); $i++) 
	{ 
	  $j = $i-1;
		$extn = $video_extn[$j];  
		$ico_im = '<img src="images/video.gif"  style="margin-top:25px; height:50px; width:50px; border:0px;">';
		
		$file_char = intval((filesize($dir.$dir_current.$video[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$video[$j].'.'.$extn));
		
		$ico_name = $video[$j];
		fileico($ico_im, $ico_name, $extn, $file_char); 
	}
}

if(isset($flash))
{
	// Выводим массив Flash	
	for ($i = 1; $i <= count($flash); $i++) 
	{ 
		$j = $i-1;
		$extn = $flash_extn[$j];  
		$ico_im = '<img src="images/flash.gif"  style="margin-top:25px; height:50px; width:50px; border:0px;">';

		$file_char = intval((filesize($dir.$dir_current.$flash[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$flash[$j].'.'.$extn));		
		
		$ico_name = $flash[$j];
		fileico($ico_im, $ico_name, $extn, $file_char);
	}
}

if(isset($pdf))
{
	// Выводим массив PDF	
	for ($i = 1; $i <= count($pdf); $i++) 
	{ 
		$j = $i-1;
		$extn = $pdf_extn[$j];  
		$ico_im = '<img src="images/pdf.gif"  style="margin-top:25px; height:50px; width:50px; border:0px;">';

		$file_char = intval((filesize($dir.$dir_current.$pdf[$j].'.'.$extn)/1000)).' КБ<br>';
		$file_char .= date ("d / m / Y H:i:s", filemtime($dir.$dir_current.$pdf[$j].'.'.$extn));
		
		$ico_name = $pdf[$j];
		fileico($ico_im, $ico_name, $extn, $file_char);
	}
} 

// =================================================================================================
?>                  
        </td>
	</tr>
	<tr>
		<td class="brw-bottom" colspan="3"></td>
	</tr>
</table>
<script type="text/javascript">
	DAN_contextmenu(200, 200, 'item_link', '');
</script>

<?


// ####### ФУНКЦИИ ###################################################################################

// ======= Функция вывода ============================================================================
function fileico($ico_im, $ico_name, $extn, $file_char)
{
	global $site, $dir_current, $fn;
	
	// если нет расширения - значит папка	
	if(!isset($extn) || $extn=="0" || $extn=="")
	{
		if($dir_current == ''){$dir_current_link = $ico_name;}else{$dir_current_link = $dir_current.$ico_name;}
		$ico_link_out = '<a class="ico_link" href="dan_browser.php?dir_current='.$dir_current_link.'&CKEditorFuncNum='.$fn.'" name="item_link">'.$ico_im.'</a>';
		$ico_name_out = '<a class="ico_link" href="dan_browser.php?dir_current='.$dir_current_link.'&CKEditorFuncNum='.$fn.'" name="item_link"><b>'.$ico_name.'</b></a>';
	}
	// если это файл		
	else
	{ 
		$ico_link_out = '<a class="ico_link" href="dan_url.php?url=/files/'.$dir_current.$ico_name.'.'.$extn.'&fn='.$fn.'" name="item_link">'.$ico_im.'</a>';
		$ico_name_out = '<a class="ico_link" href="dan_url.php?url=/files/'.$dir_current.$ico_name.'.'.$extn.'&fn='.$fn.'" name="item_link">'.$ico_name.'.'.$extn.'</a>';
	}

	echo "
	<div class=\"brw_block\">
		<div class=\"brw_im\">$ico_link_out</div>
		<div class=\"brw_txt\">$ico_name_out</div>
		<div class=\"brw_txt_2\">$file_char</div>		
	</div>";
}


// ======= Вывод списка папок слева =======================================================================
function getListFolder($dir_local, $lvl)
{
	global $dir, $dir_current, $lvl, $fn;
	if(isset($dir_local) && $dir_local != ''){$dir_this = $dir.$dir_local.'/';}else{$dir_this = $dir;}
	
	$lastchr = strrpos($dir_local, '/'); // Находит последнюю позицию символа '/' в строке	
	$folder_name = substr($dir_local, ($lastchr)); // Наименование родительской папки - отрезаем от указанного символа + 1 и до конца
	$folder_name =  str_replace('/', '', $folder_name);
	
	$lvl = intval($lvl);
	
	if($lvl == 0)
	{
		if ($dir_current == $dir_local) // активный пункт меню
		{
			echo '<a href="dan_browser.php?dir_current=&CKEditorFuncNum='.$fn.'" class="tree_menu level_'.$lvl.'_active">Корень</a>';
		}
		else
		{
			echo '<a href="dan_browser.php?dir_current=&CKEditorFuncNum='.$fn.'" class="tree_menu level_'.$lvl.'">Корень</a>';
		}	
	}
	else
	{
		if ($dir_current == $dir_local) // активный пункт меню
		{
			echo '<a href="dan_browser.php?dir_current='.$dir_local.'&CKEditorFuncNum='.$fn.'" class="tree_menu level_'.$lvl.'_active">'.$folder_name.'</a>';
		}
		else
		{
			echo '<a href="dan_browser.php?dir_current='.$dir_local.'&CKEditorFuncNum='.$fn.'" class="tree_menu level_'.$lvl.'">'.$folder_name.'</a>';
		}
		// echo $dir_local.'<br>';
		// echo $dir_this.'<br>';
		// echo  $dir_current.'<br><hr>';
	}

	$opendir = opendir($dir_this);
	
	while($file = readdir($opendir))
	{
		if (is_dir($dir_this.$file) && ($file != '.') && ($file != '..'))
		{
			if($dir_local != ''){$dir_recursion = $dir_local.'/'.$file;} else {$dir_recursion = $file;}

			// Запускаем рекурсию
			$lvl++;
			getListFolder($dir_recursion, $lvl);
		}
	}
	closedir ($opendir);
	$lvl--;	
}


// ======= Удаление директории с подпапками и файлами ===================================================

function removedir ($directory) 
{
	$dir = opendir($directory);
	while(($file = readdir($dir)))
	{
		if ( is_file ($directory."/".$file))
		{
			unlink ($directory."/".$file);
		}
		else if ( is_dir ($directory."/".$file) && ($file != ".") && ($file != ".."))
		{
			removedir ($directory."/".$file);
		}
	}
	closedir ($dir);
	rmdir ($directory);
	return TRUE;  
}

?>

</body>
</html>
