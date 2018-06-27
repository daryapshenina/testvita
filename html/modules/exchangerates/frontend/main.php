<?php
defined('AUTH') or die('Restricted access');

$date = date("d/m/Y");
$kurs_filename = $root.'/modules/exchangerates/frontend/kurs.xml';
$kurs_url =	'http://www.cbr.ru/scripts/XML_daily.asp?date_req='.$date;

if(!file_exists($kurs_filename))
{
	if(filemtime($kurs_filename) + 60*60*6 < time())
	{
		$download_file = file_get_contents($kurs_url);
		if($download_file){file_put_contents($kurs_filename, $download_file);}
	}	
}
else
{
	if(filemtime($kurs_filename) + 60*60*6 < time())
	{
		$download_file = file_get_contents($kurs_url);
		if($download_file){file_put_contents($kurs_filename, $download_file);}
	}
	$kurs_xml = simplexml_load_file($kurs_filename);

	foreach($kurs_xml as $Valute)
	{
		$val = $Valute->Value;
		$idval = $Valute['ID'];
		if($idval == "R01235") {$usd = $val;}
		if($idval == "R01239") {$eur = $val;}
	}
	
	// Заголовок модуля
	if ($m['titlepub'] == "1"){$title_out = '<div class="mod-title'.$m['module_csssuf'].'">'.$m['title'].'</div>';}
	else {$title_out = '';}

	// frontend редактирование
	if($frontend_edit == 1)
	{
		$edit_class = 'edit_mode ';
		$edit_data = 'data-type="mod_exchangerates" data-id="'.$m['id'].'"';
	}
	else
	{
		$edit_class = '';
		$edit_data = '';
	}

	echo '
		<div '.$edit_data.' id="mod_'.$m['id'].'" class="'.$edit_class.'exchangerates_main'.$m['module_csssuf'].'">
			'.$title_out.'
			<div class="dan_table_div">
				<div class="dan_table_row">
					<div class="dan_table_cell"><div class="exchangerates_main_text">USD: </div></div>
					<div class="dan_table_cell"><div class="exchangerates_main_money" id="exchangerates_main_money_usd">'.$usd.'</div></div>
				</div>
				<div class="dan_table_row">
					<div class="dan_table_cell"><div class="exchangerates_main_text">EUR: </div></div>
					<div class="dan_table_cell"><div class="exchangerates_main_money" id="exchangerates_main_money_eur">'.$eur.'</div></div>
				</div>
			</div>
		</div>
	';
}


?>