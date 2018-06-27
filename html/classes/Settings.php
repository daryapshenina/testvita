<?php
// DAN 2016

include_once $_SERVER['DOCUMENT_ROOT']."/db.php";

class Settings
{
	static public function instance()
	{
		if(Settings::$instance == null)
			Settings::$instance = new Settings();

		return Settings::$instance;
	}

	private function __construct()
	{
		global $db;

		$this->arraySettings = array();

		$SQL = $db->query('SELECT name, parameter FROM settings');
		$array = $SQL->fetchAll();

		foreach($array as $name => $value)
			$this->arraySettings[$value['name']] = $value['parameter'];
	}

	public function getValue($_name)
	{
		if(array_key_exists($_name, $this->arraySettings))
			return $this->arraySettings[$_name];

		return 'ERROR SETTINGS NAME';
	}

	public function debug()
	{
		foreach($this->arraySettings as $name => $value)
			echo $name.' => '.$value.'<br />';
	}

	static private $instance = null;
	private $arraySettings;
};

?>
