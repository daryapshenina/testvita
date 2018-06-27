<?php

include_once $root.'/classes/PHPExcel.php';

class Excel
{
	public function __construct($_pathToFile = '')
	{
		if(file_exists($_pathToFile))
			$this->excel = PHPExcel_IOFactory::load($_pathToFile);
		else
			$this->excel = new PHPExcel();

		$this->excel->setActiveSheetIndex(0); // задаем активный лист
		$this->sheet = $this->excel->getActiveSheet();
	}

	public function getCell($_x, $_y)
	{
		$_y++;
		return $this->sheet->getCellByColumnAndRow($_x, $_y)->getValue();
	}

	public function createCell($_x, $_y, $_value, $_width = -1, $_backgroundColor = '', $_textColor = '')
	{
		$column = PHPExcel_Cell::stringFromColumnIndex($_x); // получаем строковой индекс колонки
		$this->sheet->setCellValueByColumnAndRow($_x, $_y, $_value); // задаем ячейке текст

		if($_width > 0)
			$this->sheet->getColumnDimension($column)->setWidth($_width); // ширина колонки

		if(strlen($_backgroundColor) > 0)
			$this->sheet->getStyle($column.$_y)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($_backgroundColor); // цвет фона

		if(strlen($_textColor) > 0)
		{
			$arrayStyle = [
				'font'  => [
					'color' => ['rgb' => $_textColor]
				]
			];

			$this->sheet->getStyle($column.$_y)->applyFromArray($arrayStyle); // цвет текста
		}
	}

	public function getSizeY()
	{
		$y = 0;

		for(;;++$y)
		{
			if(count($this->getCell(0, $y)) == 0)
				break;
		}

		return $y;
	}

	public function getSizeX()
	{
		$x = 0;

		for(;;++$x)
		{
			if(count($this->getCell($x, 0)) == 0)
				break;
		}

		return $x;
	}

	public function fixRow($_y)
	{
		if($_y < 1)
			return;

		$_y++;

		$this->sheet->freezePane('A'.$_y);
	}

	public function setRowHeight($_height)
	{
		$_height = (int)$_height;

		if($_height <= 0)
			return;

		$this->sheet->getDefaultRowDimension()->setRowHeight($_height);
	}

	public function save($_pathToFile)
	{
		$objWriter = new PHPExcel_Writer_Excel2007($this->excel);
		$objWriter->save($_pathToFile); // сохранить файл по указанному пути
	}

	public function __destruct()
	{
		if($this->excel != null)
			unset($this->excel);
	}

	protected $excel;
	protected $sheet;
};
