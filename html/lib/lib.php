<?php
//defined('AUTH') or die('Restricted access');

include_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

// === Добавление слеша ==========================================================
// проверяем - включены ли магические кавычки и входные данные (массив?)
function addslashes_array($array) {
  return is_array($array) ?
    array_map('addslashes_array', $array) : addslashes($array);
}

// === Проверка ввода ============================================================
function zapros($str) {

	$str = mb_strtolower($str);

	//$pattern = "/(\')|(\")|(\+)|[^\x20-\xFF]/";
	// Запретим использовать любые символы, кроме букв русского и латинского алфавита, знака   "/", "-", "_", "&", "=", пробела, точки и цифр
	$pattern = "/^(\/)|([^(\w)|(\/)|(\-)||(\_)|(\[)|(\])|(\?)|(\&)|(\=)|(\.)|(\%)(\x7F-\xFF)|(\s)])/i"; // (\/) >>> удаляет первую наклонную черту >>> /page/8/ => page/8/
	$replacement = "";
	return preg_replace($pattern, $replacement, $str);
}



// === Функция для удаления опасных символов ======================================
function pregtrim($str) {
   return preg_replace("/[^\x20-\xFF]/","",@strval($str));
}



// === Проверка email =============================================================
function checkmail($mail) {
	// проверяет мыло и возвращает
	// *  +1, если мыло пустое
	// *  -1, если не пустое, но с ошибкой
	// *  строку, если мыло верное
	// режем левые символы и крайние пробелы

	$mail = trim(pregtrim($mail));
	// если пусто - выход

   	if (strlen($mail) == 0)
	{
		return 1;
	}
	else
	{
	   if (!preg_match("/^[a-z0-9\_\-\.]{1,40}@(([a-zа-я0-9\-]{1,50}+\.)+([a-zа-я]{2,12}))$/is",$mail))
	   {
			return -1;
	   }
	   else
	   {
			return $mail;
	   }
	}
}



// === Для редактора - проверка ввода ============================================================
function checkingeditor($str) {

// === Запретим использовать любые символы, кроме букв русского и латинского алфавита, знака  "/", "-", "_" и цифр
$pattern = "/[^(\w)|(\/)|(\-)|(\_)|(\s)(\x7F-\xFF)]/";
$replacement = "";
$str = preg_replace($pattern, $replacement, $str);

// Транслит
$translit = array(' ' => '_','А' => 'A','Б' => 'B','В' => 'V','Г' => 'G','Д' => 'D','Е' => 'E','Ё' => 'YO','Ж' => 'ZH','З' => 'Z','И' => 'I','Й' => 'J','К' => 'K','Л' => 'L','М' => 'M','Н' => 'N','О' => 'O','П' => 'P','Р' => 'R','С' => 'S','Т' => 'T','У' => 'U','Ф' => 'F','Х' => 'H','Ц' => 'C','Ч' => 'CH','Ш' => 'SH','Щ' => 'CSH','Ь' => '','Ы' => 'Y','Ъ' => '','Э' => 'E','Ю' => 'YU','Я' => 'YA',
'а' => 'a','б' => 'b','в' => 'v','г' => 'g','д' => 'd','е' => 'e','ё' => 'yo','ж' => 'zh','з' => 'z','и' => 'i','й' => 'j','к' => 'k','л' => 'l','м' => 'm','н' => 'n','о' => 'o','п' => 'p','р' => 'r','с' => 's','т' => 't','у' => 'u','ф' => 'f','х' => 'h','ц' => 'c','ч' => 'ch','ш' => 'sh','щ' => 'csh','ь' => '','ы' => 'y','ъ' => '','э' => 'e','ю' => 'yu','я' => 'ya',
);
$str = str_replace(array_keys($translit),array_values($translit),$str);

return ($str);
}



// === Для редактора - проверка ввода + точка ============================================================
function checkingeditor_2($str) {



// Запретим использовать любые символы, кроме букв русского и латинского алфавита, знака  "/", "-", "_", "." и цифр
$pattern = "/[^(\w)|(\/)|(\-)|(\_)|(\.)(\x7F-\xFF)]/";
$replacement = "";
$str = preg_replace($pattern, $replacement, $str);



// Транслит
$translit = array('А' => 'A','Б' => 'B','В' => 'V','Г' => 'G','Д' => 'D','Е' => 'E','Ё' => 'YO','Ж' => 'ZH','З' => 'Z','И' => 'I','Й' => 'J','К' => 'K','Л' => 'L','М' => 'M','Н' => 'N','О' => 'O','П' => 'P','Р' => 'R','С' => 'S','Т' => 'T','У' => 'U','Ф' => 'F','Х' => 'H','Ц' => 'C','Ч' => 'CH','Ш' => 'SH','Щ' => 'CSH','Ь' => '','Ы' => 'Y','Ъ' => '','Э' => 'E','Ю' => 'YU','Я' => 'YA',
'а' => 'a','б' => 'b','в' => 'v','г' => 'g','д' => 'd','е' => 'e','ё' => 'yo','ж' => 'zh','з' => 'z','и' => 'i','й' => 'j','к' => 'k','л' => 'l','м' => 'm','н' => 'n','о' => 'o','п' => 'p','р' => 'r','с' => 's','т' => 't','у' => 'u','ф' => 'f','х' => 'h','ц' => 'c','ч' => 'ch','ш' => 'sh','щ' => 'csh','ь' => '','ы' => 'y','ъ' => '','э' => 'e','ю' => 'yu','я' => 'ya',
);
$str = str_replace(array_keys($translit),array_values($translit),$str);

return ($str);
}



// ======= Определение IP- адреса =================================================================
function GetUserIP()
{
	if (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
return($ip);
}

// ======= Класс для валидации =================================================================
class classValidation
{
	public static function checkReservedWord($sourceText) // true - в случае нахождения ключевого слова
	{
		if(preg_match("/^((account)|(admin)|(ads)|(page)|(shop)|(form)|(article)|(quote)|(users_psw)|(login)|(registration)|(comments)|(notes)|(profile)|(analytics)|(subscribe)|(videochat)|(search))$/is", $sourceText))
		{
			return true;
		}
		return false;
	}

	public static function checkText($string) // true - в случае успешного прохода валидации
	{
		if(preg_match("/^[а-яёa-z0-9_\-\+\*\?\№\@\"\.\,\!\%\/\:\;\s]{0,2000}$/uis" ,$string))
		{
			return true;
		}
		return false;
	}

	public static function checkEmail($string) // true - в случае успешного прохода валидации
	{
		if(preg_match("/^[а-яёa-z0-9_\-\+\*\?\№\@\"\.\,\!\%\/\:\;\s]{0,40}$/uis" ,$string))
		{
			return true;
		}
		return false;
	}

	public static function checkPhone($string) // true - в случае успешного прохода валидации
	{
		if(preg_match("/^[а-яёa-z0-9_\-\+\*\?\№\@\"\.\,\!\%\/\:\;\(\)\s]{0,40}$/uis" ,$string))
		{
			return true;
		}
		return false;
	}

	public static function checkTypeFileAll($string) // true - в случае успешного прохода валидации
	{
		if(preg_match("/^[a-zа-я0-9\-\ \_]*[\.]((png)|(gif)|(jpg)|(jpeg)|(pdf)|(txt)|(doc)|(docx)|(zip)|(rar)|(xls)|(xlsx)|(csv))$/ui", $string))
		{
			return true;
		}
		return false;
	}
}

class classMail
{
	public static function send($to, $from, $subject, $message, $file='') // true - в случае успешной отправки
	{
		global $domain, $site;

		if(strlen($domain) === 0)
			$domain = $site;

		$data = date("d.m.Y H:i:s");;
		$ip = GetUserIP();

		$subject = '=?utf-8?B?'.base64_encode($subject).'?=';

		$from_encode = '=?UTF-8?B?'.base64_encode($from).'?=';

		$boundary = "--".md5(uniqid(time()));

		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n";

		if(empty($file['tmp_name']))
		{
			$headers .="Content-Type: text/html; charset=utf-8; \r\n";
			$headers .= "From: ".$from_encode." <no-replay@".$domain."> \r\n";
			$message = $message.'<p>Дата: '.$data.'</p>'.'<p>IP: '.$ip.'</p>';
			return mail($to, $subject, $message, $headers);
		}
		else
		{
			if(!classValidation::checkTypeFileAll($file['name']))
			{
				return false;
			}

			$file_read = fopen($file['tmp_name'], "r");
			$file_content = fread($file_read, filesize($file['tmp_name']));
			fclose($file_read);

			$headers .="Content-Type: multipart/mixed; charset=utf-8; boundary=\"$boundary\"\r\n";
			$headers .= "From: ".$from_encode." <no-replay@".$domain."> \r\n";
			$message = $message.'<p>Дата: '.$data.'</p>'.'<p>IP: '.$ip.'</p>';

			$mess = "--$boundary\n";
			$mess .= "Content-Type: text/html; charset=utf-8\n";
			$mess .= "Content-Transfer-Encoding: Quot-Printed\n";
			$mess .= "$message\n";

			$message_part = "--$boundary\n";
			$message_part .= "Content-Type: application/octet-stream\n";
			$message_part .= "Content-Transfer-Encoding: base64\n";
			$message_part .= "Content-Disposition: attachment; filename = \"".$file['name']."\"\n\n";
			$message_part .= chunk_split(base64_encode($file_content))."\n";

			$mess .= $message_part."--$boundary--\n";

			return mail($to, $subject, $mess, $headers);
		}
		return false;
	}
};


// ======= Класс для валидации =================================================================
class classInputData
{
	public static function clean ($input_data)
	{
		$out_data = trim(htmlspecialchars(strip_tags($input_data)));
		return $out_data;
	}
}


function err_mail($err)
{
	global $email, $site;

	classMail::send($email, "web@5za.ru", "Ошибка на сайте $site", $err);

	echo 'ERROR';
	exit;
}


function replace_quotes($m) {
	$pos = 0;
	$is_right_quote = 0;
	while (FALSE !== ($pos = strpos($m, '"', $pos))) 
	{
		if(!$is_right_quote) 
		{
			$m = substr_replace($m, '«', $pos, 1);
			$is_right_quote = 1;
		} 
		else 
		{
			$m = substr_replace($m, '»', $pos, 1);
			$is_right_quote = 0;
		}
		$pos += 6;
	}
	return $m;
}

?>