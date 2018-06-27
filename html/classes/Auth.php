<?php
defined('AUTH') or die('Restricted access');

class Auth
{
    public static function check() 
	{
       if(empty(self::$id)){self::getUserId();}
        return self::$id;
    }


    public static function setUserId($_id) 
	{
        self::$id = $_id;
    }


    public static function getUser()
    {
    	global $db;

    	$user_id = self::check();
	
    	if(!empty($user_id))
    	{
			$stmt_user = $db->prepare("SELECT id, cid, email, date_reg, date_visit FROM com_account_users WHERE id = :id LIMIT 1");
			$stmt_user->execute(array('id' => $user_id));
			$u = $stmt_user->fetch();
	
			return $u;
    	}
    	else {return false;}
    }

	
	public static function formReg($_title = '', $_url_return = '') // $_url_return - страница возврата после усешного прохождения регистрации
	{
		global $domain;
		
		$code = self::encode($_url_return);
		$rand = rand(1000,9999999);
		
		$out = '<form method="post" action="/account/reg">';
		$out .= '<div class="auth_form_container">';		
		$out .= '<h3 class="auth_form_title">'.$_title.'</h3>';
		$out .= '<div class="auth_form_div_email"><input class="input auth_form_email" type="email" name="email" placeholder="Email" autocomplete="off" maxlength="50" required title="Укажите корректный email"></div>';
		$out .= '<div class="auth_form_div_pass"><input class="input auth_form_pass" type="password" name="password" placeholder="Пароль" class="registration_password" autocomplete="off" maxlength="30" required pattern="[a-zA-Z0-9]{6,20}" title="Только английские буквы, и цифры без пробелов, от 6 до 20 символов"></div>';
		$out .= '<div class="auth_form_div_captcha"><img src="/administrator/captcha/pic.php?'.$rand.'" class="registration_img"><input class="input registration_captcha" type="text" name="captcha" size="4" autocomplete="off" maxlength="4" required pattern="[0-9]{4}" placeholder="Число" title="Введите 4 цифры с картинки"></div>';
		$out .= '<div class="auth_form_div_but"><input class="auth_form_but" type="submit" value="Зарегистрироваться" name="send"></div>';
		$out .= '<input type="hidden" value="'.$code.'" name="data">';
		$out .= '</div>';		
		$out .= '</form>';
		
		return $out;
	}


	public static function formLogin($_title = 'Войти', $_url_return = '') // $_url_return - страница возврата после усешного прохождения регистрации
	{
		global $domain;
		
		$code = self::encode($_url_return);
		$rand = rand(1000,9999999);
		
		$out = '<form method="post" action="/account/login">';
		$out .= '<div class="auth_form_container">';		
		$out .= '<h3 class="auth_form_title">'.$_title.'</h3>';
		$out .= '<div class="auth_form_div_email"><input class="input auth_form_email" type="email" name="email" placeholder="Email" autocomplete="off" maxlength="50" required title="Укажите корректный email"></div>';
		$out .= '<div class="auth_form_div_pass"><input class="input auth_form_pass" type="password" name="password" placeholder="Пароль" class="registration_password" autocomplete="off" maxlength="30" required pattern="[a-zA-Z0-9]{6,20}" title="Только английские буквы, и цифры без пробелов, от 6 до 20 символов"></div>';
		$out .= '<div class="auth_form_div_captcha"><img src="/administrator/captcha/pic.php?'.$rand.'" class="registration_img"><input class="input registration_captcha" type="text" name="captcha" size="4" autocomplete="off" maxlength="4" required pattern="[0-9]{4}" placeholder="Число" title="Введите 4 цифры с картинки"></div>';
		$out .= '<div class="auth_form_div_but"><input class="auth_form_but" type="submit" value="Войти" name="send"></div>';
		$out .= '<input type="hidden" value="'.$code.'" name="data">';
		$out .= '<div class="auth_form_div_forgot"><a class="auth_form_a_forgot" href="/account/restore">Забыли пароль?</a></div>';		
		$out .= '</div>';		
		$out .= '</form>';
		
		return $out;
	}

	
	public static function checkLogin($_email, $_psw)
	{
		global $db;
		
		$psw_hash = hash("sha256", 'DAN_psw'.$_email.$_psw);	// генерируем хеш пароля

		$stmt_psw = $db->prepare("SELECT id, cid, email FROM com_account_users WHERE email = :email AND psw = :psw_hash AND status = 1 LIMIT 1");
		$stmt_psw->execute(array('email'=>$_email, 'psw_hash'=>$psw_hash));

		if($stmt_psw->rowCount() > 0)
		{
			$a = $stmt_psw->fetch();
			self::setSuccess($a['id'], $a['cid']);
			return $a['id'];
		}
		
		return false;
	}
	
	
	public static function logOut()
	{
		global $domain, $qs;		
		if(self::check())
		{
			self::$id = null;
			SetCookie('uid', '', (time () - 3600), '/', '.'.$domain, False, True);		
			SetCookie('cid', '', (time () - 3600), '/', '.'.$domain, False, True);
			unset($_SESSION['uid']);
			session_destroy();
			Header ("Location: /".$qs);
			exit;
		}
	}
	
	
	// При успешному входу по логину паролю - стави сесси, куки и признак аторизации
	public static function setSuccess($_user_id, $_cid)
	{
		global $domain;

		$user_id_code = self::encode($_user_id);
		SetCookie('uid', $user_id_code, (time () + 60*60*24*365), '/', '.'.$domain, False, True); // user ID			
		SetCookie('cid', $_cid, (time () + 60*60*24*365), '/', '.'.$domain, False, True); // куки авторизации
		
		$_SESSION['uid'] = $_user_id;
		
		self::setUserId($_user_id);		
	}
	
	
	public static function encode($_str)
	{
		$base64 = base64_encode($_str);
		$len = strlen($base64);
		$j = 0;
		$arr = array();
		
		if($len > 4) 
		{
			for($i = 0; $i < $len; $i++)
			{
				if($i > 0 && ($i + 1)%5 == 0)
				{
					$j = ($i+1)/5;
					$arr[$j] = $base64[$i-2].$base64[$i].$base64[$i-3].$base64[$i-1].$base64[$i-4];
				}
			}
		}

		$remainder = '';		
		if($len%5 != 0)
		{
			for($m = 0; $m < $len%5; $m++)
			{
				$remainder .= $base64[$j*5 + $m];
			}
		}

		$code = implode($arr).$remainder;

		return $code;
	}

	
	public static function decode($_code)
	{	
		$len = strlen($_code);
		$j = 0;
		$arr = array();
		
		if($len > 4) 
		{				
			for($i = 0; $i < $len; $i++)
			{			
				if($i > 0 && ($i + 1)%5 == 0)
				{
					$j = ($i+1)/5;
					$arr[$j] = $_code[$i].$_code[$i-2].$_code[$i-4].$_code[$i-1].$_code[$i-3];
				}
			}
		}

		$remainder = '';		
		if($len%5 != 0)
		{
			for($m = 0; $m < $len%5; $m++)
			{
				$remainder .= $_code[$j*5 + $m];
			}
		}		
		
		$decode = implode($arr).$remainder;;
		$str = base64_decode($decode);

		return $str;
	}


	private static function getUserId() // Защищаем конструктор от публичного вывода - исключаем дублирование объектов.
	{
		global $db, $domain;
	
		// Если не существует сесси пользователя - подключаемся к БД и проверяем по кукам авторизацию
		if(!isset($_SESSION['uid']))
		{	
			if(isset($_COOKIE['uid']) && isset($_COOKIE['cid']) )
			{
				$uid = self::decode($_COOKIE['uid']);
	
				// Ищем пользователя в БД	
				$stmt_user = $db->prepare("SELECT id FROM com_account_users WHERE id = :uid AND cid = :cid LIMIT 1");
				$stmt_user->execute(array('uid' => $uid, 'cid' => $_COOKIE['cid']));

				if($stmt_user->rowCount() > 0) // Пользователь найден по кукам, ставим сессию
				{
					if(!isset($_SESSION)){session_start();}
					self::$user = $stmt_user->fetch();
					$_SESSION['uid'] = self::$user['id'];
					self::$id = self::$user['id'];
				}				
			}
			else
			{
				self::$id = false;
			}			
		}
		else
		{
			if(!isset($_SESSION)){session_start();}
			self::$id = $_SESSION['uid'];
		}	
	}
	
	private static $id = NULL;
	private static $user = array();

}

?>