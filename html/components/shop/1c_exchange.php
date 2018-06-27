<?php
// DAN, обновление - февраль 2014
// обмен данными с сайтом

// время работы скрипта 30 сек. + 90 сек.
set_time_limit(900);

include("../../config.php");
include("../../lib/lib.php");


// ------- Настройки -------

// логин авторизации
$login = 'admin';

// имя куки
$cookie_name = 'import_1c';

// значение куки
$cookie_value = 'import_1c_value';

// поддержка zip
$zip = 'zip=no';

// максимальный размер файла, закачиваемый на сервер
$file_limit = 'file_limit=10000000';

// шаг - сколько позиций обрабатывать за 1 раз
$item_step = 10;
$offers_step = 10;

// остановить загрузку, если формат изображения кривой ($stop_import = 1);
$stop_import = 0;


$cookie_value_input = $_COOKIE[$cookie_name];

$type = htmlspecialchars($_GET["type"]);
$mode = htmlspecialchars($_GET["mode"]);
$filename = htmlspecialchars($_GET["filename"]);

$file_post_data = file_get_contents("php://input");

$pt = $_SERVER['DOCUMENT_ROOT'];



// === MySQL ===============================================================================
$conn = mysql_connect ($host, $user, $passwd) or die ("Соединение с MySQL не установлено!");
mysql_select_db($dbname) OR die ("Соединение с базой данных не установлено");
mysql_query('SET CHARACTER SET utf8');
// === / MySQL / ===========================================================================



// Запрос пароля
$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");

while($m = mysql_fetch_array($num)):
	$setting_id = $m['id'];
	$setting_name = $m['name'];
	$setting_parameter = $m['parametr'];

	// Реквизиты для договора
	if ($setting_name == "1c_psw")
	{
		$pass = $setting_parameter;
	}
endwhile;

// $pass="test1c";
// ------- / настройки -------



// ======= HTTP АВТОРИЗАЦИЯ =====================================================================
// ------- CGI ----------------------------------------------------------------------------------
// Дописываем в .htaccess
// RewriteEngine On    # Должна уже стоять
// RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization},L]

$remote_user = $_SERVER["REMOTE_USER"]
? $_SERVER["REMOTE_USER"] : $_SERVER["REDIRECT_REMOTE_USER"];
$strTmp = base64_decode(substr($remote_user,6));
if ($strTmp)
{
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $strTmp);
}
// ------- / CGI / ------------------------------------------------------------------------------


// ------- mod_php ------------------------------------------------------------------------------
if (!isset($_SERVER['PHP_AUTH_USER']))
{
    header('WWW-Authenticate: Basic realm=""');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Доступ запрещен';
    exit;
}
else
{
	$login_in = $_SERVER['PHP_AUTH_USER'];
	$pass_in = $_SERVER['PHP_AUTH_PW'];
	// проверяем наличие недопустимых символов
	if (!preg_match("/^[a-z0-9_-]{3,20}$/is",$login_in) || !preg_match("/^[a-z0-9_-]{3,20}$/is",$_SERVER['PHP_AUTH_PW']))
	{
		echo 'Доступ запрещен - неверный логин / пароль';
		exit;
	}
	else
	{
		// Проверка логина / пароля
		if ($login === $login_in && $pass === $pass_in)
		{
			$auth_1c_exchange = 1;
		}
		else
		{
			$auth_1c_exchange = 0;
			echo 'Доступ запрещен';
			exit;
		}
	}
}
// ------- / mod_php / --------------------------------------------------------------------------
// ======= / HTTP АВТОРИЗАЦИЯ  / ================================================================




// Признак разрешённой авторизации
if ($auth_1c_exchange = 1)
{

	// Проверка на то что тип соединения - выгрузка каталога
	if ($type == "catalog")
	{
		// Если это первое соединение - передаём куки (порядковый номер обработчика) и значение куки = 0 (начальное)
		if($mode == "checkauth")
		{
			// создаем файл в котором записываем значение $item_number = 0
			$dir = "/components/shop/";
			$file = $pt.$dir.'item_number.txt';
			file_put_contents($file, '0');

			// создаем файл в котором записываем значение $	offers_number = 0
			$dir = "/components/shop/";
			$file = $pt.$dir.'offers_number.txt';
			file_put_contents($file, '0');

			// создаем файл в котором записываем значение $	offers_sum = 0
			$dir = "/components/shop/";
			$file = $pt.$dir.'item_sum.txt';
			file_put_contents($file, '0');

			// создаем файл в котором записываем значение $	offers_sum = 0
			$dir = "/components/shop/";
			$file = $pt.$dir.'offers_sum.txt';
			file_put_contents($file, '0');


			// стираем старые xml файлы
			if (file_exists($pt.'/components/shop/import_1c/import.xml'))
			{
				unlink($pt.'/components/shop/import_1c/import.xml');
			}

			if (file_exists($pt.'/components/shop/import_1c/offers.xml'))
			{
				unlink($pt.'/components/shop/import_1c/offers.xml');
			}

			// ответ для 1с
			echo "success";
		}

// ======= ИНИЦИАЛИЗАЦИЯ ========================================================================

		// Если это инициализация - передаём параметры: поддержка zip и максимальный размер файла
		if($mode == "init")
		{
			echo "$zip
$file_limit";

			// ********************
			// ПРОВЕРКА создаем файл в котором записываем значение init
			// $dir = "/components/shop/";
			// $file = $pt.$dir.'init.txt';

			// записываем файл
			// file_put_contents($file, 'init');
			// ********************
		}


// ======= ЗАГРУЗКА ФАЙЛОВ =====================================================================

		// Если это выгрузка файлов - передаём параметры: поддержка zip и максимальный размер файла
		if($mode == "file" )
		{
			// директория записи файла
			$dir = $pt."/components/shop/import_1c/";

			// какой файл получен import.xml или offers.xml или иной
			if($filename == "import.xml")
			{
				// открываем для дозаписи, если нет - создаём его
				$f_import = fopen($pt.'/components/shop/import_1c/import.xml', "a+");

				//записываем файл
				fwrite($f_import, $file_post_data);

				//закрываем файл
				fclose($f_import);


				// ------- КОЛИЧЕСТВО ЭЛЕМЕНТОВ -----------------------------------------------
				// cоздаем объект класса XMLReader:
				$reader_import_catalog_xml = new XMLReader();

				// открываем XML-файл import.xml
				if (!$reader_import_catalog_xml->open($pt.'/components/shop/import_1c/import.xml')){die('Не удалось открыть файл import.xml');}


				while ($reader_import_catalog_xml->read())
				{

					if (($reader_import_catalog_xml->nodeType == XMLReader::ELEMENT) && ($reader_import_catalog_xml->name == 'Каталог'))
					{
						$import_1c_changes = $reader_import_catalog_xml->getAttribute('СодержитТолькоИзменения');
					}


					if (($reader_import_catalog_xml->nodeType == XMLReader::ELEMENT) && ($reader_import_catalog_xml->name == 'Товар'))
					{
						// количество товаров
						$item_sum++;

						// создаем файл в котором записываем значение $item_sum
						$dir = "/components/shop/";
						$file = $pt.$dir.'item_sum.txt';

						// записываем файл
						file_put_contents($file, $item_sum);
					}
				}
				// ------- / Количество элементов / -----------------------------------------------


				// ------- ЕСЛИ ЗАГРУЗКА ПОЛНАЯ - СТИРАЕМ БД --------------------------------------
				if ($import_1c_changes == 'false') // загрузка полная
				{
					// находим старые фотографии
					$sql_item = "SELECT * FROM `com_shop_item` WHERE `id` > '0'";
					$query_item = mysql_query($sql_item) or die ($sql_item);

					while($n = mysql_fetch_array($query_item)):
						$photo_old_name = $n['photo'];
						$photobig_old_name = $n['photobig'];

						// удаляем старые фотографии
						$photo_dir = 'components/shop/photo/';
						$photo_old = $photo_dir.$photo_old_name;
						$photobig_old = $photo_dir.$photobig_old_name;
						// если есть файл изображения и его имя не пустое - удяляем файлы изображения
						if (isset($photobig_old_name) && $photobig_old_name != "")
						{
							if (file_exists($photo_old)){unlink($photo_old);}
							if (file_exists($photobig_old)){unlink($photobig_old);}
						}
					endwhile;


					mysql_query("DELETE FROM `com_shop_item` WHERE id > 0");
					mysql_query("ALTER TABLE  `com_shop_item` AUTO_INCREMENT = 1");

					// Удаляем разделы
					mysql_query("DELETE FROM `com_shop_section` WHERE `id` > '0'") or die ("Невозможно сделать выборку из таблицы - D_S");
					mysql_query("ALTER TABLE  `com_shop_section` AUTO_INCREMENT = '1'");

					// удаляем пункт меню
					mysql_query("DELETE FROM `menu` WHERE `id_com` > '0' AND `component` = 'shop' AND `main` <> '1' ");

				}
				// ------- / если загрузка полная - стираем БД / ----------------------------------

			}
			elseif($filename == "offers.xml")
			{
				// открываем для дозаписи
				$f_offers = fopen($pt.'/components/shop/import_1c/offers.xml', "a+");

				//записываем файл
				fwrite($f_offers, $file_post_data);

				//закрываем файл
				fclose($f_offers);



				// ------- КОЛИЧЕСТВО ЭЛЕМЕНТОВ -----------------------------------------------
				// cоздаем объект класса XMLReader:
				$reader_offers_catalog_xml = new XMLReader();

				// открываем XML-файл offers.xml
				if (!$reader_offers_catalog_xml->open($pt.'/components/shop/import_1c/offers.xml')){die('Не удалось открыть файл offers.xml');}


				while ($reader_offers_catalog_xml->read())
				{
					if (($reader_offers_catalog_xml->nodeType == XMLReader::ELEMENT) && ($reader_offers_catalog_xml->name == 'Предложение'))
					{
						// количество предложений
						$offers_sum++;

						// создаем файл в котором записываем значение $offers_sum
						$dir = "/components/shop/";
						$file = $pt.$dir.'offers_sum.txt';

						// записываем файл
						file_put_contents($file, $offers_sum);
					}
				}
				// ------- / Количество элементов / -----------------------------------------------



			}
			else // файлы
			{
				// это файл изображения, заносим в массив разбивку по "/"
				$path_image = split ('[/]', $filename);

				// количество элементов массива
				$ln = count($path_image);

				// пробегаемся по папкам, поэтому $i < $ln-1, а не $i <= $ln-1
				for ($i = 0; $i < $ln-1; $i++)
				{
					$d .= $path_image[$i].'/';
					// если не существует директории - создать её!
					if (!is_dir($dir.$d)){mkdir($dir.$d);}
				}

				// прописываем директорию и файл, это у нас $dir.$d.$path_image[$ln-1]
				$file_impotf_xml =  $dir.$d.$path_image[$ln-1];

				// записываем полученный файл
				file_put_contents($file_impotf_xml, $file_post_data);
			}



			// ******** ТЕСТ *******
			// ПРОВЕРКА создаем файл
			// $dir = "/components/shop/";
			// $file = $pt.$dir.'filetype.txt';
			//
			// открываем для чтения и записи; если нет файла - создаём его
			// $file_content = fopen($file,"a+");
			//
			// запираем файл
			// flock($file_content,LOCK_EX);
			//
			// $filename_w = $filename.'
			// ';
			//
			// Записываем, разблокируем, закрываем
			// fwrite($file_content,$filename_w);
			// flock($file_content,LOCK_UN);
			// fclose($file_content);
			// ******* / тест / *******



			// ответ 1С
			echo "success";
		}


// ======= Находим где находится главный пункт меню интернет-магазина =====================================================================

$query_menu_type_main_sql = mysql_query("SELECT * FROM `menu` WHERE `component` = 'shop' and `p1` = 'all' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - 5");

while($m = mysql_fetch_array($query_menu_type_main_sql))
{
	$main_type_menu = $m['menu_type'];
}


// ======= ПОШАГОВАЯ ЗАГРУЗКА =====================================================================

		// Если это пошаговая загрузка каталога
		if($mode == "import")
		{
			// cоздаем объект класса XMLReader:
			$reader_import_catalog_xml = new XMLReader();

			// открываем XML-файл import.xml
			if (!$reader_import_catalog_xml->open($pt.'/components/shop/import_1c/import.xml')){die('Не удалось открыть файл import.xml');}

			// ------- ЕСЛИ ИДЁТ ПЕРВЫЙ ПРОХОД ЗАГРУЗКИ ДАННЫХ -------------------------------------
			if($item_number == 0)
			{
				// стираем лог ошибок
				$dir = "/components/shop/";
				$file_err = $pt.$dir.'err.txt';

				file_put_contents($file_err, '');

			}
			// ------- / если идёт превый проход загрузки данных -------------------------------------



			// #######################################################################################################################
			// ======= IMPORT.XML ====================================================================================================
			$log_import_start = date("H:i:s");

			// ------- СУММА ---------------------------------------------------------------------------------------------------------
			$dir = "/components/shop/";
			$file = $pt.$dir.'item_sum.txt';

			// открываем для чтения и записи; если нет файла - создаём его
			$file_content = fopen($file,"a+");

			// запираем файл
			flock($file_content,LOCK_EX);

			// читаем 100 знаков
			$item_sum = fread($file_content,100);

			// преобразуем к числу
			$item_sum = (int)$item_sum;
			// ------- / сумма / -----------------------------------------------------------------------------------------------------
			// ------- Находим текущий элемент ---------------------------------------------------------------------------------------

			$dir = "/components/shop/";
			$file = $pt.$dir.'item_number.txt';


			// открываем для чтения и записи; если нет файла - создаём его
			$file_content = fopen($file,"a+");

			// запираем файл
			flock($file_content,LOCK_EX);

			// читаем 100 знаков
			$item_number = fread($file_content,100);

			// преобразуем к числу
			$item_number = (int)$item_number;

			// определяем шаг (выбираем следующие $item_step товаров, обрабатываем их)
			if($item_number + $item_step < $item_sum){$item_next = $item_number + $item_step;} else {$item_next = $item_sum;}

			// урезаем файл до размера "0"
			ftruncate($file_content,0);

			// Записываем, разблокируем, закрываем
			fwrite($file_content,$item_next);
			flock($file_content,LOCK_UN);
			fclose($file_content);

			// ------- / находим текущий элемент / ------------------------------------------------------------------------------------



			// cоздаем объект класса XMLReader:
			$reader_import_xml = new XMLReader();

			// открываем XML-файл import.xml
			if (!$reader_import_xml->open($pt.'/components/shop/import_1c/import.xml')){die('Не удалось открыть файл import.xml');}

			// первоначальное обнуление
			$i = 0;

			// флаг первого входа в группы - при нуле вход возможен
			$groups_flag = 0;

			// цикл делаем в том случае, если не перебрали все значения $item_number;
			if ($item_number < $item_sum)
			{
				// РАЗДЕЛЫ И ТОВАРЫ
				while ($reader_import_xml->read())
				{
					// РАЗДЕЛ
					if (($reader_import_xml->nodeType == XMLReader::ELEMENT) && ($reader_import_xml->name == 'Группы') && $item_number == 0 && $groups_flag == 0) // $item_number == 0 - первая проходка, $groups_flag == 0 первый элемент
					{
						// заносим в simple XML
						$doc_import_section = new DOMDocument('1.0', 'UTF-8');
						$section_xml = simplexml_import_dom($doc_import_section->importNode($reader_import_xml->expand(),true));

						// Разборка группы
						section_tree($section_xml,0);

						// флаг группы устанавливаем = 1
						$groups_flag = 1;
					}


					// ТОВАР
					if (($reader_import_xml->nodeType == XMLReader::ELEMENT) && ($reader_import_xml->name == 'Товар'))
					{

/*
$f_log = fopen($pt.'/components/shop/log_import11111.txt', "a+");
fwrite($f_log, $i.' _ '.$item_number."\n");
fclose($f_log);*/

						// ======= ПАЧКА ТОВАРА =============================================================================
						if ( ($i >= $item_number) && ($i < $item_next) )
						{
							// import - первоначально - сбрасываем значение
							$shop_item_ic_images = '';
							$shop_item_ic_recvisits_name = '';
							$shop_item_ic_recvisits_value = '';
							$shop_item_ic_pic_full = '';
							$pic = '';

							// заносим в simple XML
							$doc_import = new DOMDocument('1.0', 'UTF-8');
							$tovar = simplexml_import_dom($doc_import->importNode($reader_import_xml->expand(),true));

							$shop_item_ic_id = $tovar->Ид; // 1С Идентификатор
							$shop_item_ic_article = $tovar->Артикул; // 1С Артикул
							$shop_item_ic_name = $tovar->Наименование; // 1C Наименование
							$shop_item_ic_group = $tovar->Группы->Ид; // 1C Группы товара
							$shop_item_ic_description = $tovar->Описание; // Описание
							$shop_item_ic_images = $tovar->Картинка; // 1C Картинка
							$shop_item_ic_recvisits_name = $tovar->ЗначенияРеквизитов->ЗначениеРеквизита->Наименование; // Реквизиты - наименование
							$shop_item_ic_recvisits_value = $tovar->ЗначенияРеквизитов->ЗначениеРеквизита->Значение; // Реквизиты - значение

							//$shop_item_ic_description =  nl2br($shop_item_ic_description);

							$shop_item_ic_name = htmlspecialchars($shop_item_ic_name);

							//$shop_item_ic_description = preg_replace("/\r\n|\r|\n/",' ', $shop_item_ic_description);
							//$shop_item_ic_description = htmlspecialchars($shop_item_ic_description);
							$shop_item_ic_description =  nl2br($shop_item_ic_description);
							$shop_item_ic_description = addslashes($shop_item_ic_description);

							// Отрезаем 36 символов
							$shop_item_ic_id = substr($shop_item_ic_id, 0, 36);


							// ******* ЛОГ *******
							// открываем для дозаписи
							//$f_i = fopen($pt.'/components/shop/i.txt', "a+");
							//записываем файл
							//$log_i = $i.'       '.$shop_item_ic_id.'
							//';
							//fwrite($f_i, $log_i);
							//
							//закрываем файл
							//fclose($f_i);
							// ******* / лог / *******



							// ------- РАБОТА С БД --------------------------------------------------------------------

							// --- ИЩЕМ ЭТОТ ТОВАР В БАЗЕ ---
							$com_shop_item_sql_select = "SELECT * FROM `com_shop_item` WHERE `identifier` = '$shop_item_ic_id' LIMIT 1";
							$com_shop_item_sql_query = mysql_query($com_shop_item_sql_select) or die ("Невозможно сделать выборку из таблицы - $com_shop_item_sql_select");
							$com_shop_item_result = mysql_num_rows($com_shop_item_sql_query);
							// --- / ищем этот товар в базе /---

							if ($com_shop_item_result > 0) // товар уже есть в БД - обновляем его
							{
								$file_images = $pt.'/components/shop/import_1c/'.$shop_item_ic_images;

								// ----- ВЫЗОВ ФУНКЦИИ РЕСАЙЗА И ЗАГРУЗКИ ИЗОБРАЖЕНИЯ -------
								if(file_exists($file_images) && $shop_item_ic_images != "")
								{
									img_load($i);

									// ------- ОБНОВЛЯЕМ ДАННЫЕ ТОВАРА --------------------------------------------------------------
									$com_shop_item_sql_update = "UPDATE  `com_shop_item` SET  `title` =  '$shop_item_ic_name', `fulltext` = '$shop_item_ic_description', `photo` = '$photo_small', `photobig` = '$photo_big' WHERE  `identifier` = '$shop_item_ic_id' LIMIT 1 ;";
									// ------- / обновляем данные товара / ----------------------------------------------------------

									$sql_item = mysql_query($com_shop_item_sql_update) or die ("$com_shop_item_sql_update");
								}
								else
								{
									// ------- ОБНОВЛЯЕМ ДАННЫЕ ТОВАРА --------------------------------------------------------------
									$com_shop_item_sql_update = "UPDATE  `com_shop_item` SET  `title` =  '$shop_item_ic_name', `fulltext` = '$shop_item_ic_description' WHERE  `identifier` = '$shop_item_ic_id' LIMIT 1 ;";
									// ------- / обновляем данные товара / ----------------------------------------------------------

									$sql_item = mysql_query($com_shop_item_sql_update) or die ("$com_shop_item_sql_update");
								}
								// ----- / вызов функции ресайза и загрузки изображения	-------



								$sql_item = mysql_query($com_shop_item_sql_update) or die ("$com_shop_item_sql_update");
							}
							else
							{
								$file_images = $pt.'/components/shop/import_1c/'.$shop_item_ic_images;

								// ----- ВЫЗОВ ФУНКЦИИ РЕСАЙЗА И ЗАГРУЗКИ ИЗОБРАЖЕНИЯ -------
								if(file_exists($file_images) && $shop_item_ic_images != "")
								{
									img_load($i);
								}
								else {$photo_small = ""; $photo_big = "";}
								// ----- / вызов функции ресайза и загрузки изображения	-------



								// ----- ЗАНОСИМ ТОВАР В БД -----
								// Находим раздел для указанного товара
								$num = mysql_query("SELECT * FROM `com_shop_section` WHERE `identifier` LIKE '%$shop_item_ic_group%' LIMIT 1") or die ("$num");

								$result = mysql_num_rows($num);

								if ($result >= 1) // если есть такой раздел
								{
									while($m = mysql_fetch_array($num)):
										$section_id = $m['id'];
										$section_title = $m['title'];
									endwhile;

									$item_ord = $item_number + $i;


									// Вставляем в таблицу "com_shop_item"
									/*$query_insert_item = "INSERT INTO `com_shop_item` (`id`, `identifier`, `section`, `pub`, `parent`, `ordering`, `title`, `introtext`, `fulltext`, `etext_enabled`, `etext`, `price`, `priceold`, `quantity`, `photo`, `photobig`, `photomore`, `new`, `discount`, `cdate`, `tag_title`, `tag_description`)
									VALUES (NULL, '$shop_item_ic_id', '$section_id', '1', '0', '$item_ord', '$shop_item_ic_name', '', '".'$shop_item_ic_description'."', '0', '', '0', '0', '0', '$photo_small', '$photo_big', '', '', '', NOW(), '', '')";*/

									$price = getPrice($shop_item_ic_id);

									$query_insert_item = '
										INSERT INTO `com_shop_item` (
											`id`,
											`identifier`,
											`section`,
											`pub`,
											`parent`,
											`ordering`,
											`title`,
											`introtext`,
											`fulltext`,
											`etext_enabled`,
											`etext`,
											`price`,
											`priceold`,
											`quantity`,
											`photo`,
											`photobig`,
											`photomore`,
											`new`,
											`discount`,
											`cdate`,
											`tag_title`,
											`tag_description`
										)
										VALUES (
											NULL,
											"'.$shop_item_ic_id.'",
											"'.$section_id.'",
											1,
											0,
											"'.$item_ord.'",
											"'.$shop_item_ic_name.'",
											"",
											"'.$shop_item_ic_description.'",
											0,
											"",
											"'.$price.'",
											0,
											0,
											"'.$photo_small.'",
											"'.$photo_big.'",
											"",
											"",
											"",
											NOW(),
											"",
											""
										)';

									$sql_item = mysql_query($query_insert_item) or die ("Ошибка 12 - $query_insert_item");
								}
								else
								{
									$message_warning = 'Не найдена категория для товара "'.$shop_item_ic_name.'"     ';
								}
								// ----- / заносим товар в базу данных -----

							}
							// ======= / если указанный товар не существует - вставляем его / =========================
							// -------- / работа с БД / ---------------------------------------------------------------

/*
$f_log = fopen($pt.'/components/shop/log_import.txt', "a+");
$log_import = $com_shop_item_result.' _ '.$shop_item_ic_id.' _ '.$shop_item_ic_name."\n";
fwrite($f_log, $log_import);
fclose($f_log);*/

							// ******* ЛОГ *******
/*
							$log_import_end = date("H:i:s");
							// открываем для дозаписи
							$f_log = fopen($pt.'/components/shop/log_import.txt', "a+");

$log_import = $i.'  '.$log_import_start.'  '.$log_import_end.'
';

Идентификатор '.$shop_item_ic_id.'
Артикул '.$shop_item_ic_article.'
Наименование	 '.$shop_item_ic_name.'
Группы товара '.$shop_item_ic_group.'
Картинка	 '.$shop_item_ic_images.'
Описание файла '.$pic.'
------------
Есть ли товар в базе '.$com_shop_item_result.'
---------------------------------------------------------------
';

							//записываем файл
							fwrite($f_log, $log_import);

							//закрываем файл
							fclose($f_log);
*/
							// ******* / лог / *******



						}
						// ======= / пачка товара / =========================================================================


						// порядковый номер товара - увеличиваем
						$i++;
					}
				} // товары
			} // $item_number < $item_sum
			// ======= / import.xml / ================================================================================================
			// #######################################################################################################################




			// ======= / пачка предложений / =========================================================================
			// ======= / offers.xml / ================================================================================================
			// #######################################################################################################################


			if($item_number >= $item_sum)
			{
				exit('success');
			}
			else
			{
				exit('progress');
			}

		} // if($mode == "import")
	} // if ($type == "catalog")
}  //if ($auth_1c_exchange = 1)
















function getPrice($_identifier)
{
	global $pt;

	$reader_offers_xml = new XMLReader();
	if (!$reader_offers_xml->open($pt.'/components/shop/import_1c/offers.xml')){die('Не удалось открыть файл offers.xml');}

	while ($reader_offers_xml->read())
	{
		if(($reader_offers_xml->nodeType == XMLReader::ELEMENT) && ($reader_offers_xml->name == 'Предложение'))
		{
			$doc_offers = new DOMDocument('1.0', 'UTF-8');
			$xml_offers = simplexml_import_dom($doc_offers->importNode($reader_offers_xml->expand(),true));
			$shop_item_ic_id_offers_str = $xml_offers->Ид; // 1С Идентификатор
			$shop_item_ic_id_offers_str = substr($shop_item_ic_id_offers_str, 0, 36);
			$shop_item_ic_price_offers = $xml_offers->Цены->Цена->ЦенаЗаЕдиницу;
			$shop_item_ic_price_offers = (int)$shop_item_ic_price_offers;

			if($shop_item_ic_id_offers_str == $_identifier)
			{
				return $shop_item_ic_price_offers;
			}
		}
	}

	return 0;
}















// #################################################################################################################################
// #################################################################################################################################
// ####### ФУНКЦИИ #################################################################################################################
// === ФУНКЦИЯ РЕСАЙЗА И ЗАГРУЗКИ ИЗОБРАЖЕНИЯ ======================================================================================

function img_load($i)
{
	global $shop_item_ic_images, $photo_big, $photo_small, $shop_item_ic_name, $stop_import;

	$pt = $_SERVER['DOCUMENT_ROOT'];

	// --- СОЗДАЁМ ДИРЕКТОРИЮ ---
	// находим директорию
	$lastchr = strrpos($shop_item_ic_images, '/'); // находит последнюю позицию символа '/' в строке
	$f_dir = substr($shop_item_ic_images, 0, $lastchr);  // директория (символы от "0"до последнего вхождения "/")

	$folders = explode("/", $f_dir);
	$path = "";

	foreach($folders as $folder)
	{
		$path = $path.'/'.$folder;

		$folder_dir_small = $pt."/components/shop/photo/1c/small".$path;
		$folder_dir_big = $pt."/components/shop/photo/1c/big".$path;

		if(!file_exists($folder_dir_small)) // директория не существует
		{
			mkdir($folder_dir_small);
		}

		if(!file_exists($folder_dir_big)) // директория не существует
		{
			mkdir($folder_dir_big);
		}
	}
	// --- / создаём директорию ---



	$photo_small = "1c/small/".$shop_item_ic_images;
	$photo_big = "1c/big/".$shop_item_ic_images;

	$photo_small_pt = $pt.'/components/shop/photo/1c/small/'.$shop_item_ic_images;
	$photo_big_pt = $pt.'/components/shop/photo/1c/big/'.$shop_item_ic_images;

	$photo_pt = $pt.'/components/shop/import_1c/'.$shop_item_ic_images;



	// ======= ТЕСТИРОВАНИЕ =======
	//$text = $photo_small_pt.' === '.$photo_big_pt.' === '.$photo_pt;


	//$pt = $_SERVER['DOCUMENT_ROOT'];
	//$dir = "/components/shop/";
	//$file = $pt.$dir."test.txt";

	// записываем файл
	//file_put_contents($file, $text);
	// ======= / тестирование =======



	// -- РЕСАЙЗ И ПЕРЕНОС ИЗОБРАЖЕНИЯ --
	// подключение настроек
	$shop_setting_sql = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 6");

	while($m = mysql_fetch_array($shop_setting_sql)):
		$setting_id = $m['id'];
		$setting_name = $m['name'];
		$setting_parametr = $m['parametr'];

		// размер по "х" малого изображения
		if ($setting_name == "x_small")
		{
			$x_small = $setting_parametr;
		}

		// размер по "y" малого изображения
		if ($setting_name == "y_small")
		{
			$y_small = $setting_parametr;
		}

		// размер по "х" большого изображения
		if ($setting_name == "x_big")
		{
			$x_big = $setting_parametr;
		}

		// размер по "y" большого изображения
		if ($setting_name == "y_big")
		{
			$y_big = $setting_parametr;
		}

		// метод ресайза
		if ($setting_name == "small_resize_method")
		{
			$small_resize_method = $setting_parametr;
		}
	endwhile;

	$photo_pt = strtolower($photo_pt); // переводим в нижний регистр

	// для лога ошибок
	$dir = "/components/shop/";
	$file_err = $pt.$dir.'err.txt';


	// --- ОПРЕДЕЛЯЕМ РАЗМЕР ИЗОБРАЖЕНИЯ В БАЙТАХ ---
	$img_size = filesize($photo_pt);
	if ($img_size > 400000) // если изображение больше 400 кб.
	{
		//если залито что то не то, то он пошлёт нафиг и удалит залитое
		// @chmod($photo_pt,0755);
		// unlink($photo_pt);

		$err =  'ОШИБКА - размер изображения свыше 400кб. для товара "'.$shop_item_ic_name.'"
';

		$err = iconv('utf-8', 'windows-1251', $err);

		// остановить загрузку, если формат изображения кривой и в настройках стоит $stop_import == 1
		if ($stop_import == 1){echo $err;}

		// дописываем в лог ошибок
		file_put_contents($file_err, $err, FILE_APPEND);

		$photo_big = '';
		$photo_small = '';
	}
	else
	{
		// сбрасываем ошибку изображения
		$err_ph = '';

		if (isset($shop_item_ic_images) && $shop_item_ic_images != "") // если существует изображение
		{
			// игнорируем сообщение об ошибках gd библиотечки
			ini_set('gd.jpeg_ignore_warning', 1);

			// отключаем сообщение об ошибках
			error_reporting(0);

			$size = getimagesize($photo_pt);

			$width = $size[0];
			$height = $size[1];
			$type = $size[2];


			if($type == 2) // jpg
			{
				$cop = imagecreatefromjpeg($photo_pt);
			}
			elseif($type == 3) // png
			{
				$cop = imagecreatefrompng($photo_pt);
			}
			elseif($type == 1) // gif
			{
				$cop = imagecreatefromgif($photo_pt);
			}
			else
			{
				$err_photo = 1;
			}

			// если не удалось обработать изображение
			if (!$cop || $err_photo == 1)
			{
				// если залито что то не то, то он пошлёт нафиг и удалит залитое
				// @chmod($photo_pt,0755);
				// unlink($photo_pt);

				$err_ph = 1;

				$err =  'ОШИБКА - неверный формат изображения для товара "'.$shop_item_ic_name.'"
	';

				$err = iconv('utf-8', 'windows-1251', $err);

				// остановить загрузку, если формат изображения кривой и в настройках стоит $stop_import == 1
				if ($stop_import == 1){echo $err;}

				// дописываем в лог ошибок
				file_put_contents($file_err, $err, FILE_APPEND);

			}
			// Copy($photo_pt,$photo_big);

			// включаем сообщение об ошибках
			error_reporting (E_ALL);

			// -- / ресайз и перенос изображения --
		}


		if ($err_ph != 1)
		{

			// ------- БОЛЬШАЯ ФОТКА ---------------------------------------------------------------------
			//посчитаем размеры картинки
			$size = getimagesize($photo_pt);

			// обработка изображения
			$width	= $size[0];
			$height	= $size[1];

			// пересчёт размера для большой фотки (умный ресайз)
			// $x_big = '600';
			// $y_big = '450';

			$x_ratio = $x_big / $width;
			$y_ratio = $y_big / $height;
			if (($width <= $x_big) && ($height <= $y_big))
			{
				$tn_width = $width;
				$tn_height = $height;
			}
			else if (($x_ratio * $height) < $y_big)
			{
				$tn_height = $x_ratio * $height;
				$tn_width = $x_big;
			}
			else
			{
				$tn_width = $y_ratio * $width;
				$tn_height = $y_big;
			}
			// ------- / большая фотка ---------------------------------------------------------------------


			// --- Пересчёт размера для маленького изображения ---------------------------------------------
			// --- УМНЫЙ РЕСАЙЗ ---
			if ($small_resize_method == "1")
			{

				$x_ratio2 = $x_small / $width;
				$y_ratio2 = $y_small / $height;

				// если "большое изображение" меньше "малого изображения"
				if ( ($width <= $x_small) && ($height <= $y_small) )
				{
					$tn_width2 = $width;
					$tn_height2 = $height;
				}
				// находим меньшую сторону - высота меньше ширины (с учётом пропорциональности)
				else if (($x_ratio2 * $height) <= $y_small)
				{
					$tn_height2 = $x_ratio2 * $height;
					$tn_width2 = $x_small;
				}
				// ширина меньше высоты
				else
				{
					$tn_width2 = $y_ratio2 * $width;
					$tn_height2 = $y_small;
				}

				$src_x = 0;
				$src_y = 0;

				$width2 = $width;
				$height2 = $height;

			}
			// --- / умный ресайз ---


			// --- ПОДРЕЗКА ---
			elseif ($small_resize_method == "2")
			{
				$x_ratio2 = $x_small / $width;
				$y_ratio2 = $y_small / $height;

				// находим меньшую сторону - высота меньше ширины (с учётом пропорциональности)
				if (($x_ratio2 * $height) <= $x_small)
				{
					$width2 = $x_small / $y_ratio2;
					$height2 = $height;

					$src_x = ($width - $width2)/2;
					$src_y = 0;
				}

				// ширина меньше высоты
				else
				{
					$height2 = $y_small / $x_ratio2;
					$width2 = $width;

					$src_x = 0;
					$src_y = ($height - $height2)/2;
				}

				$tn_width2 = $x_small;
				$tn_height2 = $y_small;
			}
			// --- / подрезка ----


			// --- СКУКОЖИТЬ ---
			else
			{
				$tn_width2 = $x_small;
				$tn_height2 = $y_small;

				$src_x = 0;
				$src_y = 0;

				$width2 = $width;
				$height2 = $height;
			}
			// --- / скукожить ---


			// --- ПРЕОБРАЗУЕМ В НОВОЕ ИЗОБРАЖЕНИЕ ---
			// большое изображение
			$trumb = imagecreatetruecolor($tn_width, $tn_height);
			$white = imagecolorallocate($trumb, 255, 255, 255); // белый фон
			imagefilledrectangle($trumb, 0, 0, $tn_width-1, $tn_height-1, $white); // рисуем белый прямоугольник
			$image=imagecopyresampled($trumb, $cop, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);

			$trumb2 = imagecreatetruecolor($tn_width2, $tn_height2);
			$white2 = imagecolorallocate($trumb2, 255, 255, 255); // белый фон
			imagefilledrectangle($trumb2, 0, 0, $tn_width2-1, $tn_height2-1, $white2); 	// рисуем белый прямоугольник
			$image2 = imagecopyresampled($trumb2, $cop, 0, 0, $src_x, $src_y, $tn_width2, $tn_height2, $width2, $height2);

			// параметром (50) мы уменьшаем качество изображения.чтобы этого не делать поставьте "-1"
			ImageJpeg($trumb,$photo_big_pt,100);
			ImageJpeg($trumb2,$photo_small_pt,100);

			// освобождаем память и удаляем временный файл
			ImageDestroy($trumb);
			ImageDestroy($cop);
			@chmod($photo_big_pt,0644);
			@chmod($photo_small_pt,0644);


			// --- / преобразуем в новое изображение ---
		} // если изображение преобразуется без ошибок
		else
		{
			$photo_big = '';
			$photo_small = '';
		}

	} // / если размер < 400 кБ. /

	// echo "$photo_big ======= $photo_pt $width х $height =======<br /><br />";

	return $photo_big;
	return $photo_small;
	//return $message_warning;


}
// ======= / функция ресайза и загрузки изображения ========================================================



// ======= РЕКУРСИЯ ПО ГРУППАМ =============================================================================

function section_tree($section_xml, $section_parent_id)
{
	global $pt, $main_type_menu;

	// количество групп
    $count_g = count($section_xml->Группа);

	// перебираем группы
	for ($gr = 0; $gr < $count_g; $gr++)
	{
		// 1С Идентификатор
		$section_arr = $section_xml->Группа[$gr];

		$section_id = $section_arr->Ид;
		$section_name = $section_arr->Наименование;
		$section_group = $section_arr->Группы;

		$query_section_num = mysql_query("SELECT * FROM `com_shop_section` WHERE `identifier` LIKE '%$section_id%' LIMIT 1") or die ("Невозможно сделать выборку из таблицы - $com_shop_item_sql_select");
		$query_section_num_result = mysql_num_rows($query_section_num);

		// Если такой категории нет, то создаем
		if($query_section_num_result == 0)
		{
			// Вставляем данные в таблицу "com_shop_section"
			$section_insert_sql = "INSERT INTO `com_shop_section` (`id`, `identifier`, `pub`, `parent`, `ordering`, `title`, `description`, `tag_title`, `tag_description`, date)
				VALUES (NULL, '$section_id', '1', '0', '$gr', '$section_name', '', '', '', NOW())";
			$section_insert_query = mysql_query($section_insert_sql) or die ("Невозможно вставить данные - $section_insert_sql");
			$id_com = mysql_insert_id();


			// Вставляем новый пункт в таблицу меню
			$menu_insert_sql = "INSERT INTO `menu` (`id`, `menu_type`, `name`, `description`, `pub`, `parent`, `ordering`, `component`, `main`, `p1`, `p2`, `p3`, `id_com`, `prefix_css`) VALUES(NULL, '$main_type_menu', '$section_name', 'раздел интернет-магазина', '1', '$section_parent_id', '$gr', 'shop', '0', 'section', '', '', '$id_com', '')";
			$menu_insert_query = mysql_query($menu_insert_sql) or die ("Невозможно вставить данные - $menu_insert_sql");
			$menu_id = mysql_insert_id();
		}
		else
		{
			while($m = mysql_fetch_array($query_section_num))
			{
				$now_section_id = $m['id'];
			}

			// Обновляем название у раздела
			mysql_query("UPDATE `com_shop_section` SET `title` = '$section_name' WHERE `id` = '$now_section_id'");

			// Обновляем название у пункта меню
			mysql_query("UPDATE `menu` SET `name` = '$section_name' WHERE `component` = 'shop' AND `p1` = 'section' AND `id_com` = '$now_section_id'");
		}

		// Если есть вложенные группы - вызываем рекурсию
		if(isset($section_group) && $section_group != ''){section_tree($section_group, $menu_id);}
	}



}

// ======= / рекурсия по группам / =========================================================================





















?>
