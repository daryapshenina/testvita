<?php
defined('AUTH') or die('Restricted access');

include_once $root.'/db.php';

class photoSettings
{
	static public function getInstance()
	{
		if(photoSettings::$instance == null)
			photoSettings::$instance = new photoSettings();

		return photoSettings::$instance;
	}

	private function __construct()
	{
		global $db;

		$SQL = $db->query('SELECT name, value FROM com_photo_settings');
		$array = $SQL->fetchAll();

		foreach($array as $name => $value) $this->arraySettings[$value['name']] = $value['value'];	
	}

	public function getValue()
	{
		return $this->arraySettings;
	}
	
	static private $instance = null;
	private $arraySettings = array();	
}

?>