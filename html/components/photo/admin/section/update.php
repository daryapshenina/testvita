<?php
defined('AUTH') or die('Restricted access');

$section_id = intval($_POST['section_id']);
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
if ($bt_none == "Отменить"){	Header ("Location: http://".$domain."/admin"); exit;}

// условие заполненного заголовка
if ($title == "" || $menu_name == "")
{
	$err .= '<div id="main-top">Поле "Наименование раздела" или "Наименование пункта меню" не заплонено!</div>';
}
else 
{
	$stmt_section = $db->prepare("
	UPDATE com_photo_section SET
	type = :type,
	pub = :pub,
	title = :title,
	text_top = :text_top,
	text_bottom = :text_bottom,
	tag_title = :tag_title,
	tag_description = :tag_description
	WHERE id = :id
	");
	
	$stmt_section->execute(array(
	'type' => $type,
	'pub' => $title_pub,
	'title' => $title,
	'text_top' => $editor1,
	'text_bottom' => $editor2,
	'tag_title' => $tag_title,
	'tag_description' => $tag_description,
	'id' => $section_id
	));


	
	$stmt_menu = $db->prepare("
	UPDATE menu SET 
	menu_type = :menu_type,
	name = :name,
	pub = :pub,
	parent = :parent,
	ordering = :ordering
	WHERE id_com = :id_com AND component = 'photo' AND p1 = 'section'
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
				$stmt_sef = $db->prepare("SELECT id FROM url WHERE url = :url");
				$stmt_sef->execute(array('url' => 'photo/section/'.$section_id));
				
				if($stmt_sef->rowCount() == 0) // нет такого `url`
				{
					$stmt_url = $db->prepare("
					INSERT url SET
					url = :url,
					sef = :sef
					");
					
					$stmt_url->execute(array('url' => 'photo/section/'.$section_id, 'sef' => $sef));
				}
				else // Такой url есть
				{
					$url_id = $stmt_sef->fetchColumn();

					$stmt_url = $db->prepare("
					UPDATE url SET
					sef = :sef
					WHERE id = :id
					");
					
					$stmt_url->execute(array('sef' => $sef, 'id' => $url_id));					
				}
			}
		}
	}
	else
	{
		$stmt_delete = $db->prepare("DELETE FROM url WHERE url = :url");
		$stmt_delete->execute(array('url' => 'photo/section/'.$section_id));		
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
