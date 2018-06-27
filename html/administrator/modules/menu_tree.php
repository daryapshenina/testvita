<?php
// при обращении (AJAX) - выводит порядок следования и зависимость пунктров меню

include("../../config.php");

// ======= MySQL =======================================================
$db_host = $host;
$db_name = $dbname;
$db_user = $user;
$db_password = $passwd;

$db_dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
$db_opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"	
);
$db = new PDO($db_dsn, $db_user, $db_password, $db_opt);
// ======= / MySQL =====================================================


$type = $_GET["type"];
$menu_id = intval($_GET["menuid"]);
$menu_parent = intval($_GET["menuparent"]);

if($type == "left"){$menu_type = "left";}
elseif ($type == "top"){$menu_type = "top";}
else 
{
	header("HTTP/1.0 404 Not Found"); 
	include("../../404.php");
	exit;
}

// выводим меню и подменю	
echo '
	<select size="10" name="parent">
		<option value="0">Нет родительского пункта</option>
	';	

	tree(0,0); // выводим меню и подменю		
				
		echo '		
	</select> 
';


// ======= ФУНКЦИЯ - РЕКУРСИЯ ДЛЯ ВЫВОДА ПУНКТОВ МЕНЮ И ПОДМЕНЮ =======

function tree($i, $lvl) // $i = 0 начальный уровень меню, $lvl - уровень меню
{ 
	global $db, $domain, $menu_parent, $menu_type, $menu_id;  //global - уровень
	$lvl++;

	
	$stmt_menu = $db->prepare("SELECT * FROM menu WHERE menu_type = :menu_type AND parent = :parent ORDER BY ordering ASC");
	$stmt_menu->execute(array('menu_type' => $menu_type, 'parent' => $i));

	$otstup = str_repeat("&nbsp;-&nbsp;",($lvl -1));  // отступ слева у пункта меню
	
	if ($stmt_menu->rowCount() > 0) 
	{
		while($m = $stmt_menu->fetch())
		{
			$menu_id_tree = $m['id'];
			$menu_name_tree = $m['name'];
			
			if ($menu_parent == $menu_id_tree){$selected = "selected";} else {$selected = "";}
			
			// --- условия публикации ---
			if ($menu_pub == "1") 
			{
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-pub.gif" width="10" height="10">';
				$classmenu = 'menu_pub';
			}
			else 
			{
				$pub_x = '<img border="0" src="/administrator/tmp/images/p-unpub.gif" width="10" height="10">';
				$classmenu = 'menu_unpub';
			}
				
			echo '<option value="'.$menu_id_tree.'" '.$selected.' >'.$otstup.$menu_name_tree.'</option>';			
			
			tree($menu_id_tree, $lvl); // рекурсия, выводим все пункты меню, для которых этот пункт является родительским			
		}	
	} // конец проверки $result > 0
} // конец функции tree


?>