<?php
// выводит содержимое сайта в модуле (пункты меню) в админ панеле.
defined('AUTH') or die('Restricted access');

function a_menu()
{ 
	global $db, $domain;

	echo '
	<form method="POST" action="">                    
		<table class="main_tab">
	';
	
	// --- Выводим только уровни нулевого уровня ----------------------------  
	$stmt_menu = $db->query("SELECT * FROM menu WHERE id > 0 ORDER BY ordering ASC");

	while($m = $stmt_menu->fetch())
	{
		$menu_id = $m['id'];
		$menu_name = $m['name'];
		$menu_url = $m['url'];	
		$menu_pub = $m['pub'];	
		$menu_parent = $m['parent'];
		$menu_ordering = $m['ordering'];
		$menu_component = $m['component'];	

		// --- условия публикации ---
		if ($menu_pub == "1") {
			$pub_x = "<img border=\"0\" src=\"http://$domain/administrator/tmp/images/p-pub.gif\" width=\"10\" height=\"10\">";
			}
			else {
			$pub_x = "<img border=\"0\" src=\"http://$domain/administrator/tmp/images/p-unpub.gif\" width=\"10\" height=\"10\">";
			}
		
		// --- вывод пункта меню ---
		
		echo'
				<tr>
					<td class="menu-1"><input type="checkbox" name="C1" value="ON"></td>
					<td class="menu-2"><a class="menu" href="">'.$menu_name.'</a></td>
					<td class="menu-3"><input class="ordering" type="text" name="T1" value="'.$menu_ordering.'" size="3"></td>
					<td class="menu-4">$pub_x</td>
				</tr>
		';		
	}

	echo '
		</table> 
	</form>
	';

} // конец функции
?>