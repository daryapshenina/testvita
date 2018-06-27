<?php
// DAN 2015
defined('AUTH') or die('Restricted access');

const CURRENCY_RUB = 0;
const CURRENCY_USD = 1;
const CURRENCY_EUR = 2;

class CCurrency
{
	public static function update()
	{
		$root = $_SERVER['DOCUMENT_ROOT'];

		if(!file_exists($root.self::PATH_TO_FILE))
		{
			fopen($root.self::PATH_TO_FILE, "w");
			touch($root.self::PATH_TO_FILE, time() - self::TIME_TWO_DAYS);
		}

		if(filemtime($root.self::PATH_TO_FILE) + 60*60*6 < time())
		{
			$dateNow = date("d/m/Y");
			$downloadFile = file_get_contents(self::SITE.$dateNow);

			if($downloadFile)
			{
				file_put_contents($root.self::PATH_TO_FILE, $downloadFile);
			}
		}

		if(self::$usd === 0 || self::$eur === 0)
		{
			$xmlFile = simplexml_load_file($root.self::PATH_TO_FILE);

			foreach($xmlFile as $iter)
			{
				$id = $iter['ID'];
				$value = $iter->Value;

				switch($id)
				{
					case self::ID_USD:
					{
						self::$usd = floatval(str_replace(",", ".", $value));
					} break;

					case self::ID_EUR:
					{
						self::$eur = floatval(str_replace(",", ".", $value));
					} break;
				}
			}
		}
	}

	public static function getUSD()
	{
		return self::$usd;
	}

	public static function getEUR()
	{
		return self::$eur;
	}

	public static function rubToUsd($_rub)
	{
		return round($_rub / self::$usd, 2);
	}

	public static function usdToRub($_usd)
	{
		return round($_usd * self::$usd, 2);
	}

	public static function rubToEur($_rub)
	{
		return round($_rub / self::$eur, 2);
	}

	public static function eurToRub($_eur)
	{
		return round($_eur * self::$eur, 2);
	}

	private static $usd = 0;
	private static $eur = 0;

	const PATH_TO_FILE = '/temp/currency.xml';
	const SITE = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';
	const TIME_TWO_DAYS = 172800;
	const ID_USD = 'R01235';
	const ID_EUR = 'R01239';

};

?>
