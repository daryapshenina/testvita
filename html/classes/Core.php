<?php
defined('AUTH') or die('Restricted access');

class Core
{
	public $d = array();
	
	public function __construct()
	{
		global $db;

		$this->root = $_SERVER['DOCUMENT_ROOT'];

		$qs = mb_strtolower($_SERVER['REQUEST_URI']);
		$qs_arr = explode('?', $qs); // отделяем адрес от GET переменных
		$qs_arr[0] = preg_replace("/^(\/)|(\/$)/", "", $qs_arr[0]); // убираем в начале и в конце наклонную черту

		$z = explode('/', $qs_arr[0]);

		// Получаем массив ЧПУ
		$stmt_url = $db->query("SELECT url, sef FROM url");
		$u_arr = $stmt_url->fetchAll();

		if($stmt_url->rowCount() > 0)
		{
			foreach($u_arr as $u)
			{

				if($u['url'] == $qs_arr[0] && $u['sef'] != '') // если у нормального запроса есть ЧПУ - перезапрашиваем страницу с 301 ответом, с этим ЧПУ.
				{
					if(isset($qs_arr[1]) && $qs_arr[1] != '') $url = $u['sef'].'?'.$qs_arr[1]; else $url = $u['sef']; // Склеиваем 'SEF запрос'.'?'.'Переменные GET'

					header("HTTP/1.1 301 Moved Permanently");
					header("Location: /".$url);

					exit();
				}

				if($u['sef'] != '') $this->url[$u['url']] = $u['sef'];// если ЧПУ URL не пустой -> массив ЧПУ

				if($qs_arr[0] != '' && $qs_arr[0] == $u['sef'] &&  $u['sef'] != '') $z = explode('/', $this->url[$u['url']]);	// заменяем ЧПУ на внутреннee представление				
			}
		}

		$this->d = array_pad($z, 7, '');
		if(isset($qs_arr[1])) $this->r = $qs_arr[1];
	}

	public function __get($_name){
		return $this->$_name;
	}
	
	public function __set($_name, $_value){
		$this->$_name = $_value;
	}

	public function head(){
		echo $this->head;
	}	
	
	public function component($_c = 'component'){
		echo $this->$_c;
	}	

	public function description(){
		echo $this->description;
	}

	public function module($_name){
		echo $this->module[$_name];
	}

	public function title(){
		echo $this->title;
	}
	
	public function errLog($_text){
		$this->err .= $_text;		
		$debug = debug_backtrace();
		$str = date("Y-m-d H:i:s")."\nTEXT => ".$_text."\nFILE => ".$debug[0]['file']."\nLINE => ".$debug[0]['line']."\n\n";
		$file = $_SERVER["DOCUMENT_ROOT"].'/log.txt';
		$f = fopen($file,"a+");
		fwrite($f,$str);
		fclose($f);
	}

	public $domainIdn = false;
	public $component = ''; // Основное содержимое
	public $description = '';
	public $domain = '';
	public $head = '';
	public $module = array();
	public $root = '';
	public $r = ''; // Запрос, идущий после знака ?
	public $salt = 'DAN_salt'; // Соль для хешей
	public $title = '';
	public $url = array();

	public $err = false;
}