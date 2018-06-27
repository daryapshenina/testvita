<?
// DAN 2015
defined('AUTH') or die('Restricted access');

class captcha
{
	public function getCaptcha()
	{
		define("code_dir", "../captcha/codegen/");
		// что бы не кэшировалась картинка
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                     // дата в прошлом
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", 10000) . " GMT"); // 1 января 1970
		header("Cache-Control: no-store, no-cache, must-revalidate");         // HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);            // еще раз, для надежности
		header("Pragma: no-cache");                                           // HTTP/1.0
		header("Content-Type:image/png");
		 
		$linenum = 2; // Число линий (для шума в картинке)
		$img_arr = array( // массив с именами файлов-фонов
						 "codegen.png",
						 "codegen0.png"
						);
		$font_arr = array(); // массив со шрифтами
		$font_arr[0]["fname"] = "1.ttf";
		$font_arr[0]["size"] = 18;
		$font_arr[1]["fname"] = "2.ttf";
		$font_arr[1]["size"] = 28;
		$font_arr[2]["fname"] = "3.ttf";
		$font_arr[2]["size"] = 24;
		$font_arr[3]["fname"] = "4.ttf";
		$font_arr[3]["size"] = 24;
		$font_arr[4]["fname"] = "5.ttf";
		$font_arr[4]["size"] = 40;
		 
		$n = rand(0,sizeof($font_arr)-1); // выбираем шрифт
		$img_fn = $img_arr[rand(0, sizeof($img_arr)-1)]; // выбираем фон
		 
		$im = imagecreatefrompng (code_dir . $img_fn); // загружаем фон
		 
		for ($i=0; $i<$linenum; $i++) // шум в виде линий
		{
			$color = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));
			imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
		}
		 
		$color = imagecolorallocate($im, rand(0, 200), 0, rand(0, 200)); // цвет текста
		imagettftext ($im, $font_arr[$n]["size"], rand(-4, 4), rand(3, 10), rand(25, 30), $color, code_dir.$font_arr[$n]["fname"], $this->code()); //сам текст в пределах картинки
		 
		for ($i=0; $i<$linenum; $i++) // шум в виде линий
		{
			$color = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));
			imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
		}
		 
		ImagePNG ($im); // вывод изображения
		ImageDestroy ($im); // Освобождаем память
	} 	
	
	
	private function code()
	{
		$cde = rand(1000, 1999);

		session_start();
		$_SESSION['code'] = $cde;

		return $cde;
	}	
}

?>