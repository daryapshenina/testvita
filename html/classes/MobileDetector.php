<?php
//defined('AUTH') or die('Restricted access');

class MobileDetector {

	static function getDevice()
	{
		if(self::$instance == NULL)
			self::$instance = new MobileDetector();

		return self::$instance->device;
	}

	private function __construct()
	{
		$this->device = NULL;
		$this->check();
	}

	private function check()
	{
		if(!empty($_SERVER['HTTP_USER_AGENT']))
		{
			foreach(self::$arrayDevices as $device => $expr)
			{
				if(preg_match('~' . $expr . '~i', mb_strtolower($_SERVER['HTTP_USER_AGENT']), $matches))
				{
					$this->device = 'other' != $device ? $device : $matches[1];
					break;
				}
			}
		}
	}

	private static $instance;
	private $device;

	protected static $arrayDevices = array(
		"android"       => '(android)',
		"blackberry"    => '(blackberry)',
		"iphone"        => '(iphone|ipod)',
		"ipad"			=> '(ipad)',
		"opera"         => '(opera mini)',
		"palm"          => '(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)',
		"windows"       => '(iemobile|ppc|smartphone|windows phone)',
		"other"         => '(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)',
	);

}
