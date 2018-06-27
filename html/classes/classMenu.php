<?php
// ======= Публичные методы =======
// getActiveId() - возвращает id активного пункта меню
// getParentMenuId() - возвращает массив родительских пунктов меню + сам пункт
// childMenuId($menu_id) - возвращает массив дочерних пунктов меню 2 уровня (без рекурсии)
// getMenu($type, $view, $parent_id, $lvl) - вывести меню - тип, вид, родитель, начальный уровень (для рекурсии)
// ======= Приватныее свойства =======
// $idActive; - активный пункт меню
// $parentArr; - родители + пункт меню, пример: Array([name] => Раздел1 [url] => shop/section/1 ))
// leftTree(...) - развёрнутое меню
// leftMin(...) - свёрнутое меню
// leftAccordion() - аккордион

defined('AUTH') or die('Restricted access');

class classMenu
{
	public $parentArr; // родители + пункт меню (массив название + url)

	
	public function __construct()
	{
		$this->idActive = $this->ActiveId();
		$this->parentArr = array();
		$this->parentArr = $this->parentMenuId($this->idActive);	
	}	

	// Находим id активного пункта меню
	public function getActiveId()
	{
		return $this->idActive;
	}
	
	// Возвращает массив родительских пунктов меню
	public function getParentMenuId()
	{
		return $this->parentArr;
	}

	
	// возвращает массив дочерних пуктов меню (без дальнейшей рекурсии)
	public function childMenuId($menu_id)
	{
		global $db;

		$stmt_menu = $db->prepare("SELECT id, name, parent, component, p1, p2, p3, id_com FROM menu WHERE parent = :menu_id AND pub = '1' ");
		$stmt_menu->execute(array('menu_id' => $menu_id));

		$child_arr = array();
		
		while($m = $stmt_menu->fetch())
		{
			$id = $m['id'];
			$name = $m['name'];
			$parent_id = $m['parent'];
			$menu_component = $m['component'];
			$menu_p1 = $m['p1'];
			$menu_p2 = $m['p2'];
			$menu_p3 = $m['p3'];
			$menu_id_com = $m['id_com'];

			$p = $this->menuUrl($menu_component, $menu_p1, $menu_p2, $menu_p3, $menu_id_com);

			$child_arr[$id]['name'] = $name; // имя пункта меню
			$child_arr[$id]['url'] = $this->sef($p); // ЧПУ URL		
		}
		
		return $child_arr;
	}


	public function getMenu($type, $view, $parent_id, $lvl)
	{
		// $type тип 'left', 'top'
		// $view - вид (развёрнутое, свёрнутое,выпадающее, аккордион и др.)
		// $parent_id - родительский пункт меню
		// $lvl - подуровень

		global $db, $domain, $url_arr;	
		
		if(!isset($parent_id)){$parent_id = 0;}
		$lvl++;

		$stmt_numtree = $db->prepare("SELECT * FROM menu WHERE menu_type = :menu_type AND parent = :parent AND pub = '1' ORDER BY ordering ASC");
		$stmt_numtree->execute(array('menu_type' => $type, 'parent' => $parent_id));

		$result = $stmt_numtree->rowCount();
		$this->rowCount = $result;

		if ($result > 0) 
		{
			while($m = $stmt_numtree->fetch())
			{
				$menu_id = $m['id'];
				$menu_name = $m['name'];
				$menu_pub = $m['pub'];
				$menu_parent = $m['parent'];
				$menu_ordering = $m['ordering'];
				$menu_main = $m['main'];				
				$menu_component = $m['component'];
				$menu_p1 = $m['p1'];
				$menu_p2 = $m['p2'];
				$menu_p3 = $m['p3'];
				$menu_id_com = $m['id_com'];
				$menu_prefix_css = $m['prefix_css'];		

				$p = $this->menuUrl($menu_component, $menu_p1, $menu_p2, $menu_p3, $menu_id_com);

				if($p == 'page/1'){$p = '';}

				if ($type == 'left' && $view == 'tree'){$this->leftTree($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css);}				
				if ($type == 'left' && $view == 'min'){$this->leftMin($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css);}
				if ($type == 'left' && $view == 'accordion'){$this->leftAccordion($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css);}				
				if ($type == 'left' && $view == 'extension'){$this->leftExtension($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css);}
				if ($type == 'left' && $view == 'list'){$this->shopList($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css);}				
			}
		
		} // конец проверки $result > 0

		return $this->menu_out;	
	}
	
	
	// Возвращает массив родительских пунктов меню
	public function parentMenuId($menu_id)
	{
		global $db, $d;

		$stmt_parent = $db->prepare("SELECT id, name, parent, component, p1, p2, p3, id_com  FROM menu WHERE id = :menu_id ");
		$stmt_parent->execute(array('menu_id' => $menu_id));

		while($m = $stmt_parent->fetch())
		{
			$id = $m['id'];
			$name = $m['name'];
			$parent_id = $m['parent'];
			$menu_component = $m['component'];
			$menu_p1 = $m['p1'];
			$menu_p2 = $m['p2'];
			$menu_p3 = $m['p3'];
			$menu_id_com = $m['id_com'];

			$p = $this->menuUrl($menu_component, $menu_p1, $menu_p2, $menu_p3, $menu_id_com);		
		}

		if(!isset($parent_id) && ($d[0] == 'shop' || $d[0] == 'article') && $d[1] == 'item') // условие описывает товар или статью
		{
			$item_id = intval($d[2]);
			
			$stmt_item = $db->query("
			SELECT  i.section, i.title, m.id as menu_id
			FROM  com_".$d[0]."_item  i 
			JOIN  menu m ON m.id_com = i.section
			WHERE i.id = '".$item_id."' 
			AND i.pub = '1' 
			AND m.component = '".$d[0]."'
			AND m.p1 = 'section' LIMIT 1
			");	
			
			$a = $stmt_item->fetchAll();
			$parent_id = $a[0]['menu_id'];
		}
		
		if(isset($id))
		{
			$this->parentArr[$id]['name'] = $name; // имя пункта меню
			$this->parentArr[$id]['url'] = $this->sef($p); // ЧПУ URL
		}
		
		if(isset($parent_id) && $parent_id != 0)
		{
			$this->parentMenuId($parent_id);
		}

		return array_reverse($this->parentArr, true);
	}


	


	private $menu_out; // вывод меню
	private $idActive; // активный пункт меню
	private $rowCount; // количество пунктов меню, определяется в функции getMenu() 
	
	// Находим id активного пункта меню
	private function ActiveId()
	{
		global $db, $qs_arr;

		$stmt_active = $db->query("SELECT id, component, p1, p2, p3, id_com, prefix_css FROM menu WHERE pub = '1' ");

		while($a = $stmt_active->fetch())
		{
			$menu_id = $a['id'];
			$menu_component = $a['component'];
			$menu_p1 = $a['p1'];
			$menu_p2 = $a['p2'];
			$menu_p3 = $a['p3'];
			$menu_id_com = $a['id_com'];
			$menu_prefix_css = $a['prefix_css'];
			
			$p = $this->menuUrl($menu_component, $menu_p1, $menu_p2, $menu_p3, $menu_id_com);		
		
			if($qs_arr[0] == $p){$id_active = $menu_id;}			
		}
		
		if(!isset($id_active)){$id_active = '';}
	
		$this->idActive = $id_active;
	
		return $id_active;
	}	

	
	// Левое, развёрнутое меню
	private function leftTree($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css)
	{
		global $domain, $url_arr, $qs_arr;
			
		if ($qs_arr[0]  == $p || ($qs_arr[0] == "" && $p == "page/1"))
		{
			$class = 'activeleftmenu';
		}
		else
		{
			$class = 'leftmenu';
		}

		$s = $this->sef($p); // получаем ЧПУ
		if($s != ''){$s = '/'.$s;}
		
		$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';
	
		// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
		$this->getMenu($type, $view, $menu_id, $lvl);

		return $this->menu_out;
	}
	

	// Левое, свёрнутое меню
	private function leftMin($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css)
	{
		global $domain, $url_arr, $qs_arr;

		if ($qs_arr[0]  == $p || ($qs_arr[0] == "" && $p == "/page/1"))
		{
			$class = 'activeleftmenu';
		}
		else
		{
			$class = 'leftmenu';
		}

		$s = $this->sef($p); // получаем ЧПУ
		if($s != ''){$s = '/'.$s;}
		
		$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';
		
		// перебираем родителей активного пункта меню и сверяем с текущим
		foreach ($this->parentArr as $id => $z)
		{
			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			if($id == $menu_id)
			{
				$this->getMenu($type, $view, $menu_id, $lvl);
			}
		}

		return $this->menu_out;	
	}
	

	// аккордион меню
	private function leftAccordion($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css)
	{
		global $domain, $url_arr, $qs_arr;
	
		if ($qs_arr[0]  == $p || ($qs_arr[0] == "" && $p == "/page/1"))
		{
			$class = 'activeleftmenu';
			$expand = 'expand';
		}
		else
		{
			$class = 'leftmenu';
			
			$expand = '';
			
			// перебираем родителей активного пункта меню и сверяем с текущим
			foreach ($this->parentArr as $id => $z)
			{
				// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
				if($id == $menu_id)
				{
					$expand = 'expand';
				}
			}
		}
	
		// для первого уровня подключаем аккордион
		if($lvl == 1)
		{
			//для первого уровня меню, если есть подпункты - выводим аккордион
			if(count($this->childMenuId($menu_id)) > 0)
			{	
				$this->menu_out .= '<a class="left_head '.$class.'-1'.$menu_prefix_css.'" href="#">'.$menu_name.'</a>';
				$this->menu_out .= '<div class="left_body '.$expand.'" style="display: none;">';
				$this->getMenu($type, $view, $menu_id, $lvl); // рекурсия для первого уровня
				$this->menu_out .= '</div>';				
			}
			else
			{
				$s = $this->sef($p); // получаем ЧПУ
				if($s != ''){$s = '/'.$s;}			
				$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';				
			}
		}
		else
		{
			$s = $this->sef($p); // получаем ЧПУ
			if($s != ''){$s = '/'.$s;}			
			$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';

			// рекурсия для уровней, глубже первого
			$this->getMenu($type, $view, $menu_id, $lvl);			
		}	
	}


	// Левое, раскрывающееся меню
	private function leftExtension($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css)
	{
		global $domain, $url_arr, $qs_arr;

		$url_path = $qs_arr[0];
		
		if ($url_path  == $p || ($url_path == "" && $p == "/page/1"))
		{
			$class = 'activeleftmenu';
		}
		else
		{
			$class = 'leftmenu';
		}

		$s = $this->sef($p); // получаем ЧПУ
		if($s != ''){$s = '/'.$s;}
			
		if(count($this->childMenuId($menu_id)) > 0) // если есть подпункты
		{
			$this->menu_out .= '<div onmouseover="menuleft_ext(this, \'over\');" onmouseout="menuleft_ext(this, \'out\');">';
			$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';
			$this->menu_out .= '<div class="leftmenu-ext-block">';
			$this->getMenu($type, $view, $menu_id, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			$this->menu_out .= '</div>';
			$this->menu_out .= '</div>';
		}
		else
		{
			if ($lvl == 1) // для превого уровня - обычные меню
			{
				$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';			
			}	
			else // для нижних уровней - раскрывающееся
			{
				$this->menu_out .= '<a class="'.$class.'-ext'.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';			
			}
		}

		return $this->menu_out;
	}


	// Меню - лист, как у интернет-магазинов
	private function shopList($type, $view, $menu_id, $lvl, $menu_name, $menu_main, $menu_component, $p, $menu_prefix_css)
	{
		global $domain, $url_arr, $qs_arr;

		$url_path = $qs_arr[0];
		
		if ($url_path  == $p || ($url_path == "" && $p == "/page/1"))
		{
			$class = 'activeleftmenu';
		}
		else
		{
			$class = 'leftmenu';
		}

		$s = $this->sef($p); // получаем ЧПУ
		if($s != ''){$s = '/'.$s;}
		
		// Подменю 2-го и 3-го уровня		
		$child_arr_2 = $this->childMenuId($menu_id);

		if(count($child_arr_2) > 0) // если есть подпункты		
		{
			$this->menu_out .= '<div onmouseover="menu_list(this, \'over\');" onmouseout="menu_list(this, \'out\');"  onclick="menu_list_click(this, \''.$domain.$s.'\');" class="'.$class.'-'.$lvl.$menu_prefix_css.'" >'.$menu_name;			
			$this->menu_out	.= '<div class="mod_menu_list_container"><div class="mod_menu_list">';

			foreach($child_arr_2 as $child_2_id => $child_2_var_arr)
			{
				$this->menu_out .= '<div class="mod_menu_list_1_container"><a class="mod_menu_list_1" href="http://'.$domain.'/'.$child_2_var_arr['url'].'">'.$child_2_var_arr['name'];
					
				// Подменю 3 уровня
				$child_arr_3 = $this->childMenuId($child_2_id);

				if(count($child_arr_3) > 0) // если есть подпункты		
				{
					foreach($child_arr_3 as $child_3_id => $child_3_var_arr)
					{
						$this->menu_out .= '<a href="http://'.$domain.'/'.$child_3_var_arr['url'].'" class="mod_menu_list_2">'.$child_3_var_arr['name'].'</a>';
					}	
				}
				
				$this->menu_out .= '</a></div>';
			}
			
			$this->menu_out	.= '</div></div>';
			$this->menu_out .= '</div>';			
		}
		else // нет подпунктов
		{
			if ($lvl == 1) {$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';}				
		}

		return $this->menu_out;
	}	


	// возвращает URL (без домена) из параметров меню
	private function menuUrl($menu_component, $menu_p1, $menu_p2, $menu_p3, $menu_id_com)
	{
		if ($menu_component != ''){$p0 = $menu_component;}
		if ($menu_p1 != ''){$p1 = '/'.$menu_p1;} else{$p1 = '';}
		if ($menu_p2 != ''){$p2 = '/'.$menu_p2;} else{$p2 = '';}
		if ($menu_p3 != ''){$p3 = '/'.$menu_p3;} else{$p3 = '';}
		if ($menu_id_com != ''){$p4 = '/'.$menu_id_com;} else{$p4 = '';}

		$p = $p0.$p1.$p2.$p3.$p4;

		return $p;
	}
	
	
	// возвращает ЧПУ по URL path
	private function sef($p)
	{
		global $url_arr;	
		
		// если есть в массиве ЧПУ - заменяем
		if(isset($url_arr[$p]) && $url_arr[$p] != ''){$s = $url_arr[$p];}	
		else {$s = $p;}
		
		return $s;	
	}
}

?>
