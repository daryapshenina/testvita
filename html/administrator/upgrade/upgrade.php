<?php
// Закачиваем xml-файл версии установленной на сайте.
// К указанной версии прибавляем 0.01 - получаем последнюю версию обновления.
// Закачиваем файла обновления из соответствующей папки. 
// Подключаем этот файл. Этот файл вносит обновления.
// Удаляем 'administrator/upgrade/export_latest.xml' (загруженный файл для сравнения)
// Перезагружаем страницу

defined('AUTH') or die('Restricted access');

function a_com()
{ 
	global $domain, $db, $latest_version_url;
	
	// подключаем файл отправки ошибок в тех. поддержку.
	include("administrator/upgrade/mail_support.php");	
	
	// выводим шапку
	echo '
		<div id="main-top"><img border="0" src="/administrator/tmp/images/upgrade25.png" width="25" height="25"  align="middle"/>&nbsp;&nbsp;Установка обновления.</div>
		<div>&nbsp;</div>		
		</div>
		<div class="margin-left-right-10">
	';		
	
	// загрузка XML-файла версии системы управления, установленной на сайте.
	$xmlversion = simplexml_load_file('tmp/version.xml');
	
	if ($xmlversion) 
	{	
		// версия системы управления сайтом
		$version = $xmlversion->cmsversion;
		$version = (real)$version;
	}	
	
	// --- определяем номер следующей версии, которую необходимо скачать и установить ---
	// к указанной версии прибавляем 0.01 - получаем последнюю версию обновления.
	$next_version = $version + 0.01;
	$next_version = (string)$next_version;
	$next_version = str_replace('.','_',$next_version);
	
// ======= Скачиваем zip - файлы обновления =====================================================
	
	// проверка существования старого файла upgrade.zip
	if (file_exists('administrator/upgrade/upgrade/upgrade.zip'))
	{
		// удаляем upgrade.zip
		unlink('administrator/upgrade/upgrade/upgrade.zip');
	}

	$upgrade_files_zip_url = $latest_version_url.$next_version.'/upgrade.zip';

	// Проверка существования версии на удалённом хосте - читаем заголовки ответа сервера
	$headers_upgrade_files_zip_url = @get_headers($upgrade_files_zip_url);
	//print_r ($headers);
	//echo $headers[0];
	
	// === Проверяем, есть ли в заголовке 200 (файл существует) ===
	if(strpos($headers_upgrade_files_zip_url[0],'200')) 
	{
		// Закачка файла 
		$get_file = file_get_contents($upgrade_files_zip_url);
		file_put_contents('administrator/upgrade/upgrade/upgrade.zip', $get_file);
		
		echo 'Файлы обновления скопированы. <br/>';
		
		// создаём архив
		$zip = new ZipArchive();
		
		// если zip-архив удалось открыть	
		if ($zip->open('administrator/upgrade/upgrade/upgrade.zip') === true)
		{
			echo 'Обновление: <br/>';
			
			
			// ------- Файл управлением обновления ---------------------------------------------------------
		
			// файл управления обновления для скачивания
			$next_version_url = $latest_version_url.$next_version.'/upgrade.upg';	
			
			// Проверка существования версии на удалённом хосте - читаем заголовки ответа сервера
			$headers = @get_headers($next_version_url);
			//print_r ($headers);
			//echo $headers[0];
			
			// Проверяем, есть ли в заголовке 200 (файл существует)
			if(strpos($headers[0],'200')) 
			{
				// Закачка файла
				$get_file = file_get_contents($next_version_url);
				file_put_contents('administrator/upgrade/upgrade/upgradeverion.php', $get_file);
				
				include("administrator/upgrade/upgrade/upgradeverion.php");
			} 
			else 
			{
				echo '
				<div class="margin-left-right-10">
					<h4 class="red">Файл версии не найден</h4>			
				</div>	
				';
				
				// отправляем сообщение об ошибке в службу тех. поддержки
				$error = '<p>Сайт: '.$domain.'</p><p>Файл версии обновления '.$next_version_url.' не найден</p>'; 
				mailsupport($error);	
			}
					
			// удаляем export_latest.xml
			unlink('administrator/upgrade/export_latest.xml');
				
			// удаляем 'upgradeverion.php'
			unlink('administrator/upgrade/upgrade/upgradeverion.php');	
			
			// закрывающий тег
			echo "</div>";
		
			// -------------------------------------------------------------------------------------------------
			
			
			//закрытие архива
			$zip->close();			
		}
		else
		{
			echo 'Файл архива не удалось открыть. <br/>';
			
			// отправляем сообщение об ошибке в службу тех. поддержки
			$error = '<p>Сайт: '.$domain.'</p><p>Файл архива не удалось открыть.</p>'; 
			mailsupport($error);			
		}			
	} // конец действия "если есть zip - архив"	
	else 
	{
		echo '<div class="red">Файлы обновления не найдены! </div><br/>';		
	}

// =============================================================================================
// === ПРОВЕРКА ПОСЛЕДНЕЙ ВЕРСИИ ===============================================================

		// загрузка XML-файла версии системы управления, установленной на сайте.
		$xmlversion = simplexml_load_file('tmp/version.xml');
		
		if ($xmlversion) 
		{	
			// версия системы управления сайтом
			$version = $xmlversion->cmsversion;
			// переводим в цифровой формат
			$version = (real)$version;
		}	
		
		// ----------------------------------------------------------------------------
		// загрузка XML-файла последней версии системы управления с удалённого сервера.
		
		// URL файла на сервере
		$xml_url = $latest_version_url.'latest.xml';
		
		// Закачка файла XML
		$xmllatest = simplexml_load_file($xml_url);
		
		if ($xmllatest) 
		{	
			// последняя версия системы управления сайтом
			$latest = $xmllatest->cmsversion;
			$latest =(real)$latest;
		}	
		
		// проверка: установлена ли последняя версия сайта
		if ($version >= $latest) 
		{
			echo '
				<div>&nbsp;</div>
				<div class="margin-left-right-10">
					<div>Текущая версия сайта <span class="red"><b>'.$version.'</b></span></div>
					<div>Текущая версия обновления <span class="red"><b>'.$latest.'</b></span></div>
					<h4 class="green">Обновление не требуется, у вас установлена последняя версия сайта</h4>			
				</div>	
			';		
			
		}
		else 
		{
			echo '
				<div>&nbsp;</div>
				<div class="margin-left-right-10">
					<div><span class="red"><b>Требуется следующий шаг установки обновления</b></span></div>
					<div>&nbsp;</div>
					<div>Текущая версия сайта <span class="red"><b>'.$version.'</b></span></div>
					<div>Текущая версия обновления <span class="red"><b>'.$latest.'</b></span></div>
					<h4><a class="red" href="/admin/upgrade/upgrade/"><img border="0" src="/administrator/tmp/images/setup_red.png" width="306" height="83"  align="middle"/></a></h4>			
					<div>Для установки обновления потребуется некоторое время. Не закрывайте страницу.</div>
				</div>	
			';		
		}
	
} // конец функции компонента
?>