<?php
// DAN обновление - февраль 2014
// Экспорт в Excel - помещает фотографии в архив
defined('AUTH') or die('Restricted access');

// Если получена команда создать архив с фото
if ($_POST['arhiv_create'])
{
	// Удаляем если такой же архив существует
	if (file_exists($root.'/components/shop/backup_photo.zip'))
	{
		unlink($root.'/components/shop/backup_photo.zip');
	}
	
	// Создаем архив с изображениями перебирая бд товаров
	$filepath = $root.'/components/shop/backup_photo.zip';
	$zip = new ZipArchive;
	if ($zip->open($filepath, ZipArchive::CREATE) === TRUE)
	{
		$item = mysql_query("SELECT `photobig` FROM `com_shop_item`") or die ("Невозможно сделать выборку из таблицы - 1");
		while($si = mysql_fetch_array($item))
		{ 
			$photobig = $si['photobig'];
			
			if(file_exists($root.'/components/shop/photo/'.$photobig) && $photobig != '')
			{
				// откуда взять, как назвать в архиве
				$zip->addFile($root.'/components/shop/photo/'.$photobig, $photobig);
			}
		}
		$zip->close();
	}
}
// Если получена команда скачать архив
elseif ($_POST['arhiv_download'])
{
	header("Location: /components/shop/backup_photo.zip");
}

// Если получена команда удалить архив
elseif ($_POST['arhiv_delete'])
{
	if (file_exists($root.'/components/shop/backup_photo.zip'))
	{
		unlink($root.'/components/shop/backup_photo.zip');
	}
}

function a_com()
{ 
	global $site, $task, $root; 

		echo 
		'	
		<table id="main-top-tab">
			<tr>
				<td class="imshop">Экспорт данных в Excel</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<div class="margin-left-right-10">
			<div>&nbsp;</div>
			<form method="POST" enctype="multipart/form-data" action="export_excel_start">
			<div>После нажатия кнопки <b>"Скачать прайс-лист"</b> все товары размещенные в Интернет-магазине будут записаны в Excel файл после чего загрузятся на Ваш компьютер. <br /><br />Товары не имеющие <b>"Идентификатора"</b> получат его автоматически!</div><br />
			<div>Операция может занять много времени, пожалуйста, не закрывайте страницу!</div>
			<br />
			<div><input type="submit" value="Скачать прайс-лист" name="submit" class="export_load"></div>
			</form>	
		</div>
		<div>&nbsp;</div>
		<div class="margin-left-right-10">
			<div>&nbsp;</div>
			<form method="POST" enctype="multipart/form-data" action="">
			<div>Вы так же можете загрузить на свой компьютер все фотографии товаров.<br /><br />
			<b>Для этого нужно:</b><br />
			1) Нажать на кнопку <b>Поместить фотографии в архив</b>. При нажатии старый архив будет удален и заменен новым!<br />
			2) Нажать на кнопку <b>Скачать архив фотографий от <Дата созданного архива с фотографиями></b><br /><br />
			Так же Вы можете удалить уже созданный архив нажав на кнопку <b>Удалить архив с фотографиями</b> для экономии места на сервере
			<br /><br />
			<div>
				<input type="submit" value="Поместить фотографии в архив" name="arhiv_create" class="export_arhiv">
		';
		
		// Смотрим существует ли архив для активации кнопок
		if (file_exists($root.'/components/shop/backup_photo.zip'))
		{
			// Определяем дату создания архив
			$data_arhiv = date("j-m-Y", fileatime($root.'/components/shop/backup_photo.zip'));
			
			echo '
				<input type="submit" value="Скачать архив фотографий от '.$data_arhiv.'" name="arhiv_download" class="export_load">
				<input type="submit" value="Удалить архив фотографий" name="arhiv_delete" class="export_delete">
			';
		}
		else
		{
			echo '
				<input type="submit" value="Скачать архив фотографий" name="arhiv_download" class="export_load_off" disabled>
				<input type="submit" value="Удалить архив фотографий" name="arhiv_delete" class="export_delete_off" disabled>
			';
		}
			
		echo '
			</div>
			</form>	
		</div>
		';	
} // конец функции
