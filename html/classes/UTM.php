<?php
defined('AUTH') or die('Restricted access');

class UTM
{
	public function set_cookie($GET)
	{
		global $domain;


		if(isset($GET['utm_source'])) // источник перехода
		{
			setcookie('utm_source', $GET['utm_source'], (time() + 60*60*24*365), '/', '.'.$domain, False, True);
			setcookie('utm_date', date("Y-m-d H:i:s"), (time() + 60*60*24*365), '/', '.'.$domain, False, True);
			setcookie('utm_counter', 1, (time() + 60*60*24*365), '/', '.'.$domain, False, True);
		}
		if(isset($GET['utm_medium'])) setcookie('utm_medium', $GET['utm_medium'], (time() + 60*60*24*365), '/', '.'.$domain, False, True); // тип трафика
		if(isset($GET['utm_campaign'])) setcookie('utm_content', $GET['utm_content'], (time() + 60*60*24*365), '/', '.'.$domain, False, True); // дополнительная информация, которая помогает различать объявления
		if(isset($GET['utm_term'])) setcookie('utm_term', $GET['utm_term'], (time() + 60*60*24*365), '/', '.'.$domain, False, True); // ключевая фраза		
	}


	public function counter()
	{
		global $domain;	

		if(isset($_COOKIE['utm_counter']))
		{
			$counter = $_COOKIE['utm_counter'] + 1;
			setcookie('utm_counter', $counter, (time() + 60*60*24*365), '/', '.'.$domain, False, True);
		}
	}


	public function get()
	{
		global $domain;
		
		$utm_arr['utm_source'] = $this->get_utm('utm_source');
		$utm_arr['utm_medium'] = $this->get_utm('utm_medium');
		$utm_arr['utm_campaign'] = $this->get_utm('utm_campaign');
		$utm_arr['utm_content'] = $this->get_utm('utm_content');
		$utm_arr['utm_term'] = $this->get_utm('utm_term');
		$utm_arr['utm_date'] = $this->get_utm('utm_date');
		$utm_arr['utm_counter'] = $this->get_utm('utm_counter');
		
		return $utm_arr;
	}
	
	
	public function delete()
	{
		global $domain;

		setcookie('utm_source', '', (time() - 3600), '/', '.'.$domain, False, True);
		setcookie('utm_date', '', (time() - 3600), '/', '.'.$domain, False, True);
		setcookie('utm_counter', '', (time() - 3600), '/', '.'.$domain, False, True);
		setcookie('utm_medium', '', (time() - 3600), '/', '.'.$domain, False, True); // тип трафика
		setcookie('utm_campaign', '', (time() - 3600), '/', '.'.$domain, False, True); // название рекламной кампании
		setcookie('utm_content', '', (time() - 3600), '/', '.'.$domain, False, True); // дополнительная информация, которая помогает различать объявления
		setcookie('utm_term', '', (time() - 3600), '/', '.'.$domain, False, True); // ключевая фраза				
	}


	// Возвращает UTM метку из $GET или $_COOKIE
	private function get_utm($_name)
	{
		global $GET;

		if(isset($GET[$_name])){$u = $GET[$_name];}
		else
		{
			if(isset($_COOKIE[$_name])){$u = urldecode($_COOKIE[$_name]);}
			else
			{
				$u = '';
				if($_name == 'utm_date') $u = '0000-00-00 00:00:00';
			}
		}

		return $u;
	}
}

?>