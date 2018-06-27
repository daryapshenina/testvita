<?php
// выводит содержимое сайта в контенте (пункты меню) в админ панеле.
defined('AUTH') or die('Restricted access');

// подключаем компоненты
$stmt_com = $db->query("SELECT * FROM components WHERE enabled = '1' ");

while($m = $stmt_com->fetch())
{
	$components_com = $m['components'];	
	if(is_file($root."/components/".$components_com."/admin/component.php")) include($root."/components/".$components_com."/admin/component.php"); 	
}


function a_com()
{ 
	global $root, $head, $admin_d2;

	echo '         
		<table class="admin_table">
			<tr>
				<th style="width:38px;">&nbsp;</td>
				<th><div style="text-align:left; padding-left:10px;">Верхнее меню</div></td>
				<th style="text-align:left; padding-left:10px; width:298px;">Тип страницы</td>			
				<th style="width:38px;" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
			</tr>
	';
	
	tree('top',0,0);
	
	echo '
		</table>	
		<div class="menusep"></div>
		<table class="admin_table">
			<tr>
				<th style="width:38px;">&nbsp;</td>
				<th><div style="text-align:left; padding-left:10px;">Левое меню</div></td>
				<th style="text-align:left; padding-left:10px; width:298px;">Тип страницы</td>		
				<th style="width:38px;" title="Публикация. Зелёным цветом обозначены опубликованные пункты, серым - неопубликованные"align="center">Пб.</td>
			</tr>	
	';
	tree('left',0,0);
	echo '</table>';

} // конец функции a_com


########### ФУНКЦИИ ##############################################################################################
// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ (ГЛАВНОЕ МЕНЮ) =========================

function tree($menu_type, $i, $lvl) // $menu_type 1 - верхнее 2 - левое  $i = 0 начальный уровень меню, $lvl - уровень меню
{
	global $db, $domain;
	$lvl++;
	
	$stmt_menu = $db->prepare("SELECT * FROM menu WHERE menu_type = :type AND parent = :parent ORDER BY ordering ASC");
	$stmt_menu->execute(array('type' => $menu_type, 'parent' => $i));
	
	if ($stmt_menu->rowCount() > 0) 
	{
		$otstup = str_repeat("&nbsp;&nbsp;-&nbsp;&nbsp;",($lvl-1));  // отступ слева у пункта меню
	
		while($m = $stmt_menu->fetch())
		{
			$menu_id = $m['id'];
			$menu_name = $m['name'];
			$menu_description = $m['description'];	
			$menu_pub = $m['pub'];	
			$menu_parent = $m['parent'];
			$menu_ordering = $m['ordering'];
			$menu_component = $m['component'];
			$menu_main = $m['main'];
			$menu_p1 = $m['p1'];
			$menu_p2 = $m['p2'];
			$menu_p3 = $m['p3'];
			$menu_id_com = $m['id_com'];
			
			// первоначальный сброс 	
			$s_public = "";
			$cell_title = "";
			$ra = "";
			$lock = ''; 
			$access = '';					
		
			
			// ======= Подключаем раздел компонента =======
			
			// ----- PAGE -----
			if ($menu_component == "page")
			{
				$stmt_page = $db->prepare("SELECT access FROM com_page WHERE id = :id LIMIT 1");
				$stmt_page->execute(array('id' => $menu_id_com));
				
				if ($stmt_page->fetchColumn() == 1)
				{
					$lock = '<img alt="страница закрыта паролем" title="страница закрыта паролем" src="/administrator/tmp/images/access.png">'; 
					$access = '<span class="red"> закрыта паролем</span>';
				}
			}
			// ----- / PAGE -----				

			
			// ----- ИНТЕРНЕТ - МАГАЗИН -----
			if ($menu_component == "shop")
			{
				$stmt_section = $db->prepare("SELECT pub FROM com_shop_section WHERE id = :id LIMIT 1");
				$stmt_section->execute(array('id' => $menu_id_com));

				// условия публикации пункта меню
				if ($stmt_section->fetchColumn() == "1") 
				{
					$cell_title = "раздел магазина опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && isset($section_pub) == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел магазина опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел магазина скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				// определяем количество товаров в категории
				$stmt_item = $db->prepare("SELECT id FROM com_shop_item WHERE section = :section ORDER BY ordering ASC");
				$stmt_item->execute(array('section' => $menu_id_com));
	
				if ($stmt_item->rowCount() > 0){$ra = '<span class = "result-shop">('.$stmt_item->rowCount().')</span>';}
				else {$ra = "";}
			}
			// ----- / ИНТЕРНЕТ - МАГАЗИН -----	
			
			
			// ----- PHOTO -----
			if ($menu_component == "photo")
			{
				$stmt_section = $db->prepare("SELECT pub FROM com_photo_section WHERE id = :id LIMIT 1");
				$stmt_section->execute(array('id' => $menu_id_com));

				// условия публикации пункта меню
				if ($stmt_section->fetchColumn() == "1") 
				{
					$cell_title = "раздел фотогалереи опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && isset($section_pub) == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел фотогалереи опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел фотогалереи скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				// определяем количество изображений в категории
				$stmt_item = $db->prepare("SELECT id FROM com_photo_items WHERE section = :section");
				$stmt_item->execute(array('section' => $menu_id_com));
	
				if ($stmt_item->rowCount() > 0){$ra = '<span class = "photo-result">('.$stmt_item->rowCount().')</span>';}
				else {$ra = "";}
			}			
			// ----- / PHOTO -----
			
			
			// ----- СТАТЬИ -----			
			if ($menu_component == "article")
			{
				$stmt_as = $db->prepare("SELECT pub FROM com_article_section WHERE id = :id LIMIT 1");
				$stmt_as->execute(array('id' => $menu_id_com));
				
				// условия публикации пункта меню
				if ($menu_pub == "1") 
				{
					$cell_title = "раздел архива опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && $stmt_as->rowCount() == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел архива опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел архива скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				
				// определяем количество статей в категории
				$stmt_ai = $db->prepare("SELECT id FROM com_article_item WHERE section = :section ORDER BY ordering ASC");
				$stmt_ai->execute(array('section' => $menu_id_com));		
				$result_article =$stmt_ai->rowCount();

				if ($result_article != "" && $menu_main != "1" ){$ra = '<span class = "result-article">('.$result_article.')</span>';}
				else {$ra = "";}				
			}
			// ----- / СТАТЬИ -----			
			
			
			// ----- ЦИТАТЫ -----			
			if ($menu_component == "quote")
			{
				$stmt_article = $db->prepare("SELECT pub FROM com_quote_section WHERE id = :id LIMIT 1");
				$stmt_article->execute(array('id' => $menu_id_com));		
				$section_pub = $stmt_article->rowCount();
				
				// условия публикации пункта меню
				if ($menu_pub == "1") 
				{
					$cell_title = "раздел архива опубликован, пункт меню опубликован";
				}
				elseif ($menu_pub == "0" && $section_pub == "1") 
				{
					$s_public = "spub";
					$cell_title = "раздел архива опубликован, но пункт меню скрыт"; // всплывающая подсказка
				}
				else 
				{
					$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
					$public = "menu_unpub";
					$cell_title = "раздел архива скрыт, пункт меню скрыт";
				}
				// --- /опубликован ли раздел ---	
				
				
				// определяем количество статей в категории
				$stmt_qi = $db->prepare("SELECT id FROM com_quote_item WHERE section_id = :section ORDER BY ordering ASC");
				$stmt_qi->execute(array('section' => $menu_id_com));	
				$result_quote = $stmt_qi->rowCount();

				if ($result_quote > 0 && $menu_p1 == "section" ){$ra = '<span class = "result-quote">('.$result_quote.')</span>';}
				else {$ra = "";}				
			}
			// ----- / ЦИТАТЫ -----						
			// ======= / Подключаем раздел компонента =======	
			
			
			
			// для url
			if ($menu_id_com != ""){$m_id_com = '/'.$menu_id_com;} else {$m_id_com = "";}			
			
			// дополнительные параметры
			if ($menu_p1 != ""){$p1 = '/'.$menu_p1;} else {$p1 = "";}
			if ($menu_p2 != ""){$p2 = '/'.$menu_p2;} else {$p2 = "";}
			if ($menu_p3 != ""){$p3 = '/'.$menu_p3;} else {$p3 = "";}
			
			$p = $p1.$p2.$p3;
			
			// тип меню
			if ($menu_type == "top"){$menu_t = 1;}
			if ($menu_type == "left"){$menu_t = 2;}
			
			// класс главной страницы
			if($menu_main == "1")
			{
				$main = "main";
				$bold = 'bold ';
				$ico = $menu_component.'_ico';
			} 
			elseif ($menu_p1 == "authors") 
			{
				$main = 'authors';
				$bold = 'bold ';
				$ico = '';			
			}			
			else 
			{
				$main = '';
				$bold = '';
				$ico = '';			
			}	
			
			// строка компонента
			$com_class = $menu_component.'_bg';
			
			// условия публикации 
			if ($menu_pub == "1") 
			{
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10" title="опубликовано">';
				$public = "menu_pub";
				}
				else {
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10" title="не опубликовано">';
				$public = "menu_unpub ".$s_public;
			}

				// выводим содержимое			
				echo'			
					<tr class="admin_table_tr_3 '.$com_class.' '.$bold.'">
						<td class="cell_ico '.$ico.'"  title="'.$cell_title.'">'.$lock.'</td>
						<td class="contextmenu_'.$menu_component.$main.'"  data-id="'.$menu_id_com.'" title="'.$cell_title.'"><a class="'.$menu_component.$main.' '.$public.'" name="'.$menu_id_com.'"  href="/admin/com/'.$menu_component.$p.$m_id_com.'" >'.$otstup.$menu_name.'</a> '.$ra.'</td>						
						<td class="'.$public.'"  title="'.$cell_title.'">'.$menu_description.$access.'</td>
						<td>'.$pub_x.'</td>
					</tr>			
				';	
	
			// рекурсия, выводим все пункты меню, для которых этот пункт является родительским
			tree($menu_type, $menu_id, $lvl); 
			
		}	
		
	} // конец проверки $result > 0
} // конец функции tree


?>
