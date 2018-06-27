<?php
defined('AUTH') or die('Restricted access');

if(isset($_POST['title_pub'])){$title_pub = intval($_POST['title_pub']);}else{$title_pub = 0;}
if(isset($_POST['menu_pub'])){$menu_pub = intval($_POST['menu_pub']);}else{$menu_pub = 0;}
$title = trim(htmlspecialchars($_POST['title']));
$type = intval($_POST['photo_type']);
$menu_name = trim(htmlspecialchars($_POST["menu"]));
$menu_type = $_POST['menu_type'];
if(isset($_POST['parent'])){$parent = intval($_POST['parent']);}else{$parent = 0;}
$ordering = intval($_POST['ordering']);
$tag_title = trim(htmlspecialchars($_POST['tag_title']));
$tag_description = trim(htmlspecialchars($_POST['tag_description']));
$sef = checkingeditor($_POST['sef']);
$editor1 = $_POST['editor1'];
$editor2 = $_POST['editor2'];

$keywords = '';

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){	Header ("Location: http://".$domain."/admin/com"); exit;}

// условие заполненного заголовка
if ($title == "" || $menu_name == "")
{
	$err .= '<div id="main-top">Поле "Наименование раздела" или "Наименование пункта меню" не заплонено!</div>';
}
else 
{
	// Вставляем в таблицу "com_photo_section"	
	$stmt_section = $db->prepare("
	INSERT INTO com_photo_section SET
	type = :type,
	pub = :pub,
	title = :title,
	text_top = :text_top,
	text_bottom = :text_bottom,
	keywords = :keywords,
	tag_title = :tag_title,
	tag_description = :tag_description,
	views = '0',
	likes = '0',
	comments = '0'
	");
	
	$stmt_section->execute(array(
	'type' => $type,
	'pub' => $title_pub,
	'title' => $title,
	'text_top' => $editor1,
	'text_bottom' => $editor2,
	'keywords' => $keywords,
	'tag_title' => $tag_title,
	'tag_description' => $tag_description,
	));
	
	$section_id = $db->lastInsertId();

	
	// Находим все пункты меню, следующие за этим	
	$stmt_menu = $db->prepare("SELECT * FROM menu WHERE menu_type = :menu_type AND parent = :parent AND ordering >= :ordering");
	$stmt_menu->execute(array('menu_type' => $menu_type, 'parent' => $parent, 'ordering' => $ordering));


	// Обновляем порядок следования
	$i = $ordering;
	while($m = $stmt_menu->fetch())
	{
		$stmt_ordering = $db->prepare("UPDATE menu SET ordering = :ordering WHERE id = :id");
		$stmt_ordering->execute(array('ordering' => $i, 'id' => $m['id']));
	}

	
	// Вставляем новый пункт в таблицу меню
	$stmt_menu = $db->prepare("
	INSERT INTO menu SET 
	menu_type = :menu_type,
	name = :name,
	description = '',
	pub = :pub,
	parent = :parent,
	ordering = :ordering,
	component = 'photo',
	main = '0',
	p1 = 'section',
	p2 = '',
	p3 = '',
	id_com = :id_com,
	prefix_css = ''
	");
	
	$stmt_menu->execute(array(
	'menu_type' => $menu_type, 
	'name' => $menu_name,
	'pub' => $menu_pub,
	'parent' => $parent,
	'ordering' => $ordering,
	'id_com' => $section_id
	));	


	// --- ЧПУ URL ---
	if($sef != '')
	{
		if(classvalidation::checkReservedWord($sef)) // зарезервированно
		{
			$sef_err = 1;
		}
		else
		{
			// проверяем на символы
			if (!preg_match("/^[a-z0-9-_ \/]{1,255}$/is",$sef))
			{
				$sef_err = 1;
			}
			else
			{
				$sef = strtolower($sef); // в нижний регистр

				// проверяем - есть ли уже запись для `sef`
				$stmt_url = $db->prepare("SELECT id FROM url WHERE url = :url");
				$stmt_url->execute(array('url' => 'photo/section/'.$section_id));
				
				if($stmt_url->rowCount() == 0) // нет такого `url`
				{
					$stmt_url = $db->prepare("
					INSERT INTO url SET
					url = :url,
					sef = :sef
					");
					
					$stmt_url->execute(array('url' => 'photo/section/'.$section_id, 'sef' => $sef));
				}
			}
		}
	}

	if($bt_save == 'Сохранить'){Header ("Location: http://".$domain."/admin"); exit;}
	else {Header ("Location: http://".$domain."/admin/com/photo/section/edit/".$section_id); exit;}
}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
