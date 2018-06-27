<?php
// ======= ��������� ������ =======
// getActiveId() - ���������� id ��������� ������ ����
// getParentMenuId() - ���������� ������ ������������ ������� ���� + ��� �����
// childMenuId($menu_id) - ���������� ������ �������� ������� ���� 2 ������ (��� ��������)
// getMenu($type, $view, $parent_id, $lvl) - ������� ���� - ���, ���, ��������, ��������� ������� (��� ��������)
// ======= ���������� �������� =======
// $idActive; - �������� ����� ����
// $parentArr; - �������� + ����� ����, ������: Array([name] => ������1 [url] => shop/section/1 ))
// leftTree(...) - ���������� ����
// leftMin(...) - �������� ����
// leftAccordion() - ���������

defined('AUTH') or die('Restricted access');

class classMenu
{
	public $parentArr; // �������� + ����� ���� (������ �������� + url)

	
	public function __construct()
	{
		$this->idActive = $this->ActiveId();
		$this->parentArr = array();
		$this->parentArr = $this->parentMenuId($this->idActive);	
	}	

	// ������� id ��������� ������ ����
	public function getActiveId()
	{
		return $this->idActive;
	}
	
	// ���������� ������ ������������ ������� ����
	public function getParentMenuId()
	{
		return $this->parentArr;
	}

	
	// ���������� ������ �������� ������ ���� (��� ���������� ��������)
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

			$child_arr[$id]['name'] = $name; // ��� ������ ����
			$child_arr[$id]['url'] = $this->sef($p); // ��� URL		
		}
		
		return $child_arr;
	}


	public function getMenu($type, $view, $parent_id, $lvl)
	{
		// $type ��� 'left', 'top'
		// $view - ��� (����������, ��������,����������, ��������� � ��.)
		// $parent_id - ������������ ����� ����
		// $lvl - ����������

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
		
		} // ����� �������� $result > 0

		return $this->menu_out;	
	}
	
	
	// ���������� ������ ������������ ������� ����
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

		if(!isset($parent_id) && ($d[0] == 'shop' || $d[0] == 'article') && $d[1] == 'item') // ������� ��������� ����� ��� ������
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
			$this->parentArr[$id]['name'] = $name; // ��� ������ ����
			$this->parentArr[$id]['url'] = $this->sef($p); // ��� URL
		}
		
		if(isset($parent_id) && $parent_id != 0)
		{
			$this->parentMenuId($parent_id);
		}

		return array_reverse($this->parentArr, true);
	}


	


	private $menu_out; // ����� ����
	private $idActive; // �������� ����� ����
	private $rowCount; // ���������� ������� ����, ������������ � ������� getMenu() 
	
	// ������� id ��������� ������ ����
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

	
	// �����, ���������� ����
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

		$s = $this->sef($p); // �������� ���
		if($s != ''){$s = '/'.$s;}
		
		$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';
	
		// ��������, ������� ��� ������ ����, ��� ������� ���� ����� �������� ������������
		$this->getMenu($type, $view, $menu_id, $lvl);

		return $this->menu_out;
	}
	

	// �����, �������� ����
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

		$s = $this->sef($p); // �������� ���
		if($s != ''){$s = '/'.$s;}
		
		$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';
		
		// ���������� ��������� ��������� ������ ���� � ������� � �������
		foreach ($this->parentArr as $id => $z)
		{
			// ��������, ������� ��� ������ ����, ��� ������� ���� ����� �������� ������������
			if($id == $menu_id)
			{
				$this->getMenu($type, $view, $menu_id, $lvl);
			}
		}

		return $this->menu_out;	
	}
	

	// ��������� ����
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
			
			// ���������� ��������� ��������� ������ ���� � ������� � �������
			foreach ($this->parentArr as $id => $z)
			{
				// ��������, ������� ��� ������ ����, ��� ������� ���� ����� �������� ������������
				if($id == $menu_id)
				{
					$expand = 'expand';
				}
			}
		}
	
		// ��� ������� ������ ���������� ���������
		if($lvl == 1)
		{
			//��� ������� ������ ����, ���� ���� ��������� - ������� ���������
			if(count($this->childMenuId($menu_id)) > 0)
			{	
				$this->menu_out .= '<a class="left_head '.$class.'-1'.$menu_prefix_css.'" href="#">'.$menu_name.'</a>';
				$this->menu_out .= '<div class="left_body '.$expand.'" style="display: none;">';
				$this->getMenu($type, $view, $menu_id, $lvl); // �������� ��� ������� ������
				$this->menu_out .= '</div>';				
			}
			else
			{
				$s = $this->sef($p); // �������� ���
				if($s != ''){$s = '/'.$s;}			
				$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';				
			}
		}
		else
		{
			$s = $this->sef($p); // �������� ���
			if($s != ''){$s = '/'.$s;}			
			$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';

			// �������� ��� �������, ������ �������
			$this->getMenu($type, $view, $menu_id, $lvl);			
		}	
	}


	// �����, �������������� ����
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

		$s = $this->sef($p); // �������� ���
		if($s != ''){$s = '/'.$s;}
			
		if(count($this->childMenuId($menu_id)) > 0) // ���� ���� ���������
		{
			$this->menu_out .= '<div onmouseover="menuleft_ext(this, \'over\');" onmouseout="menuleft_ext(this, \'out\');">';
			$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';
			$this->menu_out .= '<div class="leftmenu-ext-block">';
			$this->getMenu($type, $view, $menu_id, $lvl); // ��������, ������� ��� ������ ����, ��� ������� ���� ����� �������� ������������
			$this->menu_out .= '</div>';
			$this->menu_out .= '</div>';
		}
		else
		{
			if ($lvl == 1) // ��� ������� ������ - ������� ����
			{
				$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';			
			}	
			else // ��� ������ ������� - ��������������
			{
				$this->menu_out .= '<a class="'.$class.'-ext'.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';			
			}
		}

		return $this->menu_out;
	}


	// ���� - ����, ��� � ��������-���������
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

		$s = $this->sef($p); // �������� ���
		if($s != ''){$s = '/'.$s;}
		
		// ������� 2-�� � 3-�� ������		
		$child_arr_2 = $this->childMenuId($menu_id);

		if(count($child_arr_2) > 0) // ���� ���� ���������		
		{
			$this->menu_out .= '<div onmouseover="menu_list(this, \'over\');" onmouseout="menu_list(this, \'out\');"  onclick="menu_list_click(this, \''.$domain.$s.'\');" class="'.$class.'-'.$lvl.$menu_prefix_css.'" >'.$menu_name;			
			$this->menu_out	.= '<div class="mod_menu_list_container"><div class="mod_menu_list">';

			foreach($child_arr_2 as $child_2_id => $child_2_var_arr)
			{
				$this->menu_out .= '<div class="mod_menu_list_1_container"><a class="mod_menu_list_1" href="http://'.$domain.'/'.$child_2_var_arr['url'].'">'.$child_2_var_arr['name'];
					
				// ������� 3 ������
				$child_arr_3 = $this->childMenuId($child_2_id);

				if(count($child_arr_3) > 0) // ���� ���� ���������		
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
		else // ��� ����������
		{
			if ($lvl == 1) {$this->menu_out .= '<a class="'.$class.'-'.$lvl.$menu_prefix_css.'" href="http://'.$domain.$s.'">'.$menu_name.'</a>';}				
		}

		return $this->menu_out;
	}	


	// ���������� URL (��� ������) �� ���������� ����
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
	
	
	// ���������� ��� �� URL path
	private function sef($p)
	{
		global $url_arr;	
		
		// ���� ���� � ������� ��� - ��������
		if(isset($url_arr[$p]) && $url_arr[$p] != ''){$s = $url_arr[$p];}	
		else {$s = $p;}
		
		return $s;	
	}
}

?>
