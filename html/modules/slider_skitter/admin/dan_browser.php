<?php
// DAN 2010
// defined('AUTH') or die('Restricted access');

include("../../../config.php");
include("../../../lib/lib.php");

include("../../../administrator/login.php");

$pt=$_SERVER['DOCUMENT_ROOT'];
$dir = "$pt/files/"; // Путь до пользовательских файлов

// ======= Получаем данные, фильтруем ============================================================
$act = checkingeditor($_GET["act"]);
$d = settype($act,'integer');
$name = checkingeditor($_REQUEST["name"]);
$ex = checkingeditor($_GET["ex"]);
$filename = checkingeditor($_POST["filename"]);
$subdir = checkingeditor($_REQUEST["subdir"]); // Подпапка
$fn = intval($_GET["CKEditorFuncNum"]);

// Данные загрузки файла
$file_name = checkingeditor_2($_FILES['filename']['name']); // Оригинальное имя файла на компьютере клиента. 
$file_type = checkingeditor_2($_FILES['filename']['type']); // Mime-тип файла, в случае, если браузер предоставил такую информацию. Пример: "image/gif". 
$file_size = checkingeditor_2($_FILES['filename']['size']); // Размер в байтах принятого файла. 
$tmp_name = $_FILES['filename']['tmp_name']; // Временное имя, с которым принятый файл был сохранен на сервере. 

// ======= Дериктории ===============================================================================

if (!isset($subdir) || $subdir == "") // Поддиректория
{
	$subdir = ""; // Полученная поддиректория
	$subd = "";  // Обработанная директория
	$topfolder = ""; // "Вверх" - родительская папка
}
else
{
	$subd = "$subdir/";	
	$lastchr = strrpos($subdir, '/'); // Находит последнюю позицию символа '/' в строке
	$topfolderdir = substr($subdir, 0, $lastchr);  // Директория родительской папки		
	$topfoldername = substr($subdir, $lastchr);   // Наименование родительской папки
	$topfoldername = str_replace('/', '', $topfoldername);
	
	$topfolder = "
	<div class=\"brw_block\">
		<div class=\"brw_im\"><a class=\"ico_link\" href=\"dan_url.php\"><img border=\"0\" src=\"images/topfolder.png\" width=\"50\" height=\"50\" align=\"middle\"></a></div>
		<div class=\"brw_txt\"><a class=\"ico_link_top\" href=\"dan_url.php\"><b>МОДУЛЬ</b></a></div>
	</div>	
	";
}

$directory = $dir.$subd.$name; // данные для удаления или создания директории - старое название файла / папки
$directoryout = $dir.$subd;    // данные для вывода директории, удаления или создания директории - путь для нового файла / папки 

// ####### УСЛОВИЯ ###################################################################################
// ======= Переименовать файл ========================================================================

if ($act == "3")
{
	if ($ex == '' || !isset ($ex)) // Нет расширения - признак папки - удаляем папку
	{
		$oldnamefolder = $directory;		
		$newnamefolder = $directoryout.$filename;
		if(file_exists($newnamefolder))  
		{  
			 if(is_dir($newnamefolder)) 
			 { 
			 	$alert = '<script type="text/javascript">alert("Папка с таким именем уже существует!");</script>';  
			 } 
		}
			else 
			{
				$filename = strtolower($filename);
				$newnamefolder = $directoryout.$filename;				
				
				rename ($oldnamefolder, $newnamefolder);	
			}			
	}
	else  // если есть расширение - значит файл - удаляем файл
	{  
		$oldnamefile = $directory.'.'.$ex;
		$newnamefile = $directoryout.$filename.'.'.$ex;	
		if (file_exists($newnamefile)) // проверяем, существует ли файл с таким именем
		{ 
			// выводимое сообщение
			$alert = '<script type="text/javascript">alert("Файл с таким именем уже существует!");</script>';
		}
		else 
		{
			$filename = strtolower($filename);
			$ex = strtolower($ex);
			$newnamefile = $directoryout.$filename.'.'.$ex;	
			
			rename ("$oldnamefile", "$newnamefile");	
		}
	} 
}

// === Удалить файл =====================================================================================

if ($act == "4")
{
	if ($ex == '' || !isset ($ex)) // Нет расширения - признак папки - удаляем папку
	{
		if(file_exists($directory))
		{
			removedir($directory);
		}					
	}
	else  // если есть расширение - значит файл - удаляем файл
	{
		if(file_exists($directory.".".$ex))
		{
			unlink($directory.".".$ex);
		}		
	} 

}
	
// ======= Создать новую папку =========================================================================

if ($act == "5")
{
	if(file_exists($directory))  
	{  
		if(is_dir($directory)) 
		{ 
			$alert = '<script type="text/javascript">alert("Папка с таким именем уже существует!");</script>';  
		} 
	}
	else 
	{
		$alert = "";
		$directory = strtolower($directory); // переводим в нижний регистр
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
				
				if(preg_match("/(\.jpg)|(\.jpeg)|(\.gif)|(\.png)/", $file_name)) // Проверка 
				{
					$alert = "";
					$ln_file_name = strlen($file_name); // Количество символов в именим файла
					if ($ln_file_name > 14) // Обрезаем длинну
					{
						$file_nm_1 = substr($file_name, 0, 9);
						$file_name = $file_nm_1.'.'.$file_name_arr[1]; // приклеиваем разрешение
					}
				//	$file_name=substr($file_name, 0, 10);
					move_uploaded_file($tmp_name, $dir.$subd.$file_name); // Копируем временный файл в указанную директорию с указанным именем	
					@chmod($dir.$subd.$file_name,0644);
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
<script src="../../../js/jquery.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
								   
	$('a.modal[href^=#]').click(function() {
		var popID = $(this).attr('rev'); 
		var popURL = $(this).attr('href'); 
				
		var query= popURL.split('?');
		var dim= query[1].split('&');
		var popWidth = dim[0].split('=')[1]; 
 
		$('#' + popID).fadeIn().css({ 'width': Number( popWidth ) }).prepend('<a href="#"class="overlay_close"><span class="btn_close" title="Закрыть окно" ></span></a>');
		
		var popMargTop = ($('#' + popID).height() + 80) / 2;
		var popMargLeft = ($('#' + popID).width() + 80) / 2;
		
		$('#' + popID).css({ 
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
		
		$('body').append('<div id="fade"></div>'); 
		$('#fade').css({'filter' : 'alpha(opacity=50)'}).fadeIn(); 
		
		return false;
	});
	
		$('a.overlay_close, #fade').live('click', function() { 
		$('#fade , .overlay').fadeOut(function() {
		$('#fade, a.overlay_close').remove();  
	}); 
		
		return false;
	});
	 
		
});



/* === RENAME функция выводит название файла и изображение в модальном окне RENAME === */
function danelmren(setup) {	
	var iname = setup.iconame; /* Имя файла  */
	var ext = setup.extn;	   /* Расширение файла */
	var sbd = setup.subdir;	   /* Поддиректория */
	
	/* Кнопка "Не переименовывать" с GET - параметрами */	
	var filenorename = '<a href=\"dan_browser.php?subdir='+sbd+'&CKEditorFuncNum=<? echo"$fn"; ?>\"><img border=\"0\" src=\"images/nodelete.png\" width=\"50\" height=\"50\" align=\"middle\" hspace=\"10\">Не переименовывать</a>';
	document.getElementById("filenorename").innerHTML = filenorename;	
	
	/* Если это папка - точку не выводим */
	if (ext==''){var tochka=''; var soobsh='Старое название папки:';} else{var tochka='.'; var soobsh='Старое название файла:';}	
/* --- выводим форму ---------------------------------------- */	
	var filerename = soobsh+' <b>'+iname+tochka+ext+'</b><br/><br/><form method=\"POST\" action=\"dan_browser.php?act=3&subdir='+sbd+'&name='+iname+'&ex='+ext+'&CKEditorFuncNum=<? echo"$fn"; ?>\"><p><input value=\"'+iname+'\" type=\"text\" name=\"filename\" size=\"30\"><input type=\"submit\" value=\"Переименовать\" name=\"bt\"></p></form>';	
	document.getElementById("filerename").innerHTML = filerename;		
}

/* === DELETE функция выводит название файла и изображение в модальном окне DELETE === */
function danelmdel(setup) {
	var iname = setup.iconame; /* Имя файла  */
	var ext = setup.extn;	   /* Расширение файла */
	var sbd = setup.subdir;	   /* Поддиректория */
	if (sbd != ''){sbd = sbd + '/'} 
	
/* --- выводим картинку в зависимости от расширения файла --- */
	/* Папка */
	if (ext==''){ danout='<img border=\"0\" src=\"images/folder.png\" width=\"50\" height=\"50\" align=\"middle\">'+iname; }
	/* Изображение */	
	if (ext=='jpg'||ext=='jpeg'||ext=='gif'||ext=='png'){ danout='<img border=\"0\" src=\"<? echo"http://$site/"; ?>files/'+sbd+iname+'.'+ext+'\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }
	/* Документ Word */
	if (ext=='doc'||ext=='docx'||ext=='rtf'){ danout='<img border=\"0\" src=\"images/doc.gif\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }
	/* Excel */
	if (ext=='xls'||ext=='xlsx'){ danout='<img border=\"0\" src=\"images/excel.gif\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }
	/* Архив */
	if (ext=='rar'||ext=='zip'){ danout='<img border=\"0\" src=\"images/rar.gif\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }	
	/* Видео */
	if (ext=='avi'||ext=='mpg'||ext=='mpeg'){ danout='<img border=\"0\" src=\"images/video.gif\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }	
	/* Flash */
	if (ext=='swf'){ danout='<img border=\"0\" src=\"images/flash.gif\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }
	/* PDF */
	if (ext=='pdf'){ danout='<img border=\"0\" src=\"images/pdf.gif\" width=\"50\" height=\"50\" align=\"middle\"> '+iname; }	

/* --- Выводим данные --- */

	/* Картинку и имя файла */
	document.getElementById("filenamedel").innerHTML = danout;	
	
	/* Кнопка "Удалить" с GET - параметрами */
	var filedelete = '<a href=\"dan_browser.php?act=4&subdir='+sbd+'&name='+iname+'&ex='+ext+'&CKEditorFuncNum=<? echo"$fn"; ?>\"><img border=\"0\" src=\"images/delete.png\" width=\"50\" height=\"50\" align=\"middle\" hspace=\"10\">Да</a>';	
	document.getElementById("filedelete").innerHTML = filedelete;	
	
	/* Кнопку "Не удалять" с GET - параметрами */
	var filenodelete = '<a href=\"dan_browser.php?subdir='+sbd+'&CKEditorFuncNum=<? echo"$fn"; ?>\"><img border=\"0\" src=\"images/nodelete.png\" width=\"50\" height=\"50\" align=\"middle\" hspace=\"10\">Не удалять</a>';
	document.getElementById("filenodelete").innerHTML = filenodelete;	
}

</script>
</head>
<body>
<? echo $alert; ?>
<table class="brw-main-tab">
	<tr>
		<td id="brw-center">
<? 
// ======= Выводим все папки и файлы ==============================================================
//echo "************** $directoryout *******************";
echo $topfolder; // Родительская папка

if ($handle = opendir($directoryout))
{
	while (false !==($file = readdir($handle)))
	{
    	if ($file !='.'&& $file !='..')
		{				
// Разбиваем на расширения	
			$l_file = strtolower($file); // переводим в нижний регистр
			$file_arr = preg_split('/\./', $l_file, -1 , PREG_SPLIT_NO_EMPTY); 
			$name = $file_arr[0]; // имя файла.
			$ext = $file_arr[1];  // расширение.
// Находим папку и переносим в массив
			if ($ext =="" ){ $folder[] = $name; }
			if ($ext =="jpg" || $ext =="jpeg" || $ext =="gif" || $ext =="png" ){ $images[]=$name; $images_extn[]=$ext; }
			if ($ext =="doc" || $ext =="docx" || $ext =="rtf" ){ $doc[] = $name; $doc_extn[] = $ext;}
			if ($ext =="xls" || $ext =="xlsx" ){ $excel[] = $name; $excel_extn[] = $ext;}
			if ($ext =="rar" || $ext =="zip"){ $rar[] = $name; $rar_extn[] = $ext;}
			if ($ext =="avi" || $ext =="mpg" || $ext =="mpeg" ){ $video[] = $name; $video_extn[] = $ext;}		
			if ($ext =="swf" ){ $flash[] = $name; $flash_extn[] = $ext;}	
			if ($ext =="pdf" ){ $pdf[] = $name; $pdf_extn[] = $ext;}				
		}
	}
    	closedir($handle);
}
			
// Выводим массив папок	
for ($i = 1; $i <= count($folder); $i++) 
{ 
  	$j = $i-1;
	$extn = "";  
	$ico_im = "<img border=\"0\" src=\"images/folder.png\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$folder[$j]";
  	fileico($ico_im, $ico_name, $extn);
} 

// Выводим массив изображений	
for ($i = 1; $i <= count($images); $i++) 
{ 
   $j = $i-1;
	$extn = $images_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"http://$site/files/$subd$images[$j].$extn\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$images[$j]";
  	fileico($ico_im, $ico_name, $extn);
} 

// Выводим массив Word	
for ($i = 1; $i <= count($doc); $i++) 
{ 
 	 $j = $i-1;
	$extn = $doc_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"images/doc.gif\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$doc[$j]";
  	fileico($ico_im, $ico_name, $extn);
} 

// Выводим массив Excel	
for ($i = 1; $i <= count($excel); $i++) 
{ 
  	$j = $i-1;
	$extn = $excel_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"images/excel.gif\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$excel[$j]";
  	fileico($ico_im, $ico_name, $extn);
} 

// Выводим массив Архив	
for ($i = 1; $i <= count($rar); $i++) 
{ 
  	$j = $i-1;
	$extn = $rar_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"images/rar.gif\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$rar[$j]";
  	fileico($ico_im, $ico_name, $extn); 
} 

// Выводим массив Видео	
for ($i = 1; $i <= count($video); $i++) 
{ 
  $j = $i-1;
	$extn = $video_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"images/video.gif\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$video[$j]";
  	fileico($ico_im, $ico_name, $extn); 
} 

// Выводим массив Flash	
for ($i = 1; $i <= count($flash); $i++) 
{ 
   	$j = $i-1;
	$extn = $flash_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"images/flash.gif\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$flash[$j]";
  	fileico($ico_im, $ico_name, $extn); 
} 

// Выводим массив PDF	
for ($i = 1; $i <= count($pdf); $i++) 
{ 
  	$j = $i-1;
	$extn = $pdf_extn[$j];  
	$ico_im = "<img border=\"0\" src=\"images/pdf.gif\" width=\"50\" height=\"50\" align=\"middle\">";
	$ico_name = "$pdf[$j]";
  	fileico($ico_im, $ico_name, $extn); 
} 

// =================================================================================================
?>                  
        </td>
	</tr>
	<tr>
		<td id="brw-bottom">
        	<br/>
        	<div id="newfolder">&nbsp;</div>
         	<div id="newfile">
            <img border="0" src="images/download.png" width="50" height="50" align="left" hspace="10">
            	Загрузить новый файл. &nbsp;&nbsp;<font size="2">Размер файла до 2Mb.</font><br/>
                <form method="POST" enctype="multipart/form-data" action="dan_browser.php?act=6&subdir=<? echo $subdir; ?>&CKEditorFuncNum=<? echo"$fn"; ?>">
                    <input type="file" name="filename" size="30">&nbsp;<input type="submit" value="Загрузить" name="B1">
                </form>            
            </div>           
        </td>
	</tr>
</table>

<?


// ======= Модальное окно "Переименовать?" =========================================================
echo "
	<div class=\"overlay\" id=\"rename\">
		<div class=\"send_title\">Переименовать <br/><br/></div>
			<div class=\"dialogue\">&nbsp;</div>
			<div class=\"dialogue\"><span id=\"filenorename\">filenorename</span></div>
			<div>&nbsp;</div>
			<hr/>
	    	<div class=\"dialogue_image\"><span id=\"filerename\">filerename</span></div>				
	</div>
";

// ======= Модальное окно "Удалить?" =========================================================
echo "
	<div class=\"overlay\" id=\"delete\">
		<div class=\"send_title\">Удалить? <br/><br/></div>
		<div class=\"dialogue\"><span id=\"filedelete\">filedelete</span></div>
		<div class=\"dialogue\"><span id=\"filenodelete\">filenodelete</span></div>
		<div>&nbsp;</div>		
		<hr/>		
	    <div class=\"dialogue_image\"><span id=\"filenamedel\">filenamedel</span>	</div>
	</div>
";

// ####### ФУНКЦИИ ###################################################################################

// ======= Функция вывода ============================================================================
function fileico($ico_im, $ico_name, $extn)
{
	global $site, $subd ,$subdir, $fn;
	
// если нет расширения - значит папка	
	if(!isset($extn) || $extn=="0" || $extn=="")
	{
		$ico_link_out="<a class=\"ico_link\" href=\"dan_browser.php?subdir=$subd$ico_name&CKEditorFuncNum=".$fn."\">$ico_im</a>";
		$ico_name_out="<a class=\"ico_link\" href=\"dan_browser.php?subdir=$subd$ico_name&CKEditorFuncNum=".$fn."\"><b>$ico_name</b></a>";
	}
// если это файл		
	else
	{ 
		$ico_link_out="<a class=\"ico_link\" href=\"dan_url.php?url=$site/files/".$subd."$ico_name.$extn&fn=".$fn."\">$ico_im</a>";
		$ico_name_out="<a class=\"ico_link\" href=\"dan_url.php?url=$site/files/".$subd."$ico_name.$extn&fn=".$fn."\">$ico_name.$extn</a>";
	}

	echo "
	<div class=\"brw_block\">
		<div class=\"brw_im\">$ico_link_out</div>
		<div class=\"brw_txt\">$ico_name_out</div>
		<div class=\"brw_ico\">		
			<div title=\"Переименовать\" class=\"rename_file\"><a href=\"#?w=600\" rev=\"rename\" class=\"modal\" onClick='danelmren({iconame:\"$ico_name\", extn:\"$extn\", subdir:\"$subdir\"});'><img border=\"0\" src=\"images/rename_file.gif\" width=\"16\" height=\"16\" align=\"middle\"></a></div>
			<div title=\"Удалить\" class=\"rename_file\" ><a href=\"#?w=600\" rev=\"delete\" class=\"modal\" onClick='danelmdel({iconame:\"$ico_name\", extn:\"$extn\", subdir:\"$subdir\"});'><img border=\"0\" src=\"images/delete_file.gif\" width=\"16\" height=\"16\" align=\"middle\"></a></div>
		</div>
	</div>";
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