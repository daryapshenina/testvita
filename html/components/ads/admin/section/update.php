<?php
defined('AUTH') or die('Restricted access');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/ads/admin/section/image_resize.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/ads/classes/AdsSection.php');

$id = intval($d[5]);

if(isset($_POST['menu_pub'])){$menu_pub = intval($_POST['menu_pub']);}else{$menu_pub = 0;}
$title = trim(htmlspecialchars($_POST['title']));
$pub = intval($_POST['pub']);
$menu_name = trim(htmlspecialchars($_POST["menu"]));
$menu_type = $_POST['menu_type'];
if(isset($_POST['parent'])){$parent = intval($_POST['parent']);}else{$parent = 0;}
$ordering = intval($_POST['ordering']);
$tag_title = trim(htmlspecialchars($_POST['tag_title']));
$tag_description = trim(htmlspecialchars($_POST['tag_description']));
$sef = checkingeditor($_POST['sef']);
$editor1 = $_POST['editor1'];
$editor2 = $_POST['editor2'];

if(isset($_POST["bt_save"])){$bt_save = $_POST["bt_save"];} else {$bt_save = '';}  // кнопка 'Сохранить'
if(isset($_POST["bt_prim"])){$bt_prim = $_POST["bt_prim"];} else {$bt_prim = '';}  // кнопка 'Применить'
if(isset($_POST["bt_none"])){$bt_none = $_POST["bt_none"];} else {$bt_none = '';}  // кнопка 'Отменить'

// ======= Условия ==================================================================
// Условие - отменить
if ($bt_none == "Отменить"){Header ("Location: /admin/com"); exit;}

// условие заполненного заголовка
if ($title == "" || $menu_name == "")
{
	$err .= '<div id="main-top">Поле "Наименование раздела" или "Наименование пункта меню" не заплонено!</div>';
}
else 
{
	$section = new AdsSection();
	$section->tag_title = $tag_title;
	$section->tag_description = $tag_description;
	$section->text_top = $editor1;
	$section->text_bottom = $editor2;	

	$options = serialize($section);
	
	$file_name = mb_strtolower($_FILES['file']['name']);

	if(!empty($file_name)) $SQL_image = "image = '1',";
	else $SQL_image = '';

	// Вставляем в таблицу "com_ads_section"	
	$stmt_section = $db->prepare("
	UPDATE com_ads_section SET
	title = :title,
	".$SQL_image."
	pub = :pub,
	options = :options
	WHERE id = :id
	");
	
	$stmt_section->execute(array(
	'title' => $title,
	'pub' => $pub,
	'options' => $options,
	'id' => $id
	));

	// Обработка изображения
	ads_image_resize($id);

	
	// Вставляем новый пункт в таблицу меню
	$stmt_menu = $db->prepare("
	UPDATE menu SET 
	menu_type = :menu_type,
	name = :name,
	pub = :pub,
	parent = :parent,
	ordering = :ordering
	WHERE id_com = :id_com AND component = 'ads' AND p1 = 'section'
	LIMIT 1
	");
	
	$stmt_menu->execute(array(
	'menu_type' => $menu_type, 
	'name' => $menu_name,
	'pub' => $pub,
	'parent' => $parent,
	'ordering' => $ordering,
	'id_com' => $id
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
				$stmt_url->execute(array('url' => 'ads/section/'.$id));
				
				if($stmt_url->rowCount() == 0) // нет такого `url`
				{
					$stmt_url = $db->prepare("
					INSERT INTO url SET
					url = :url,
					sef = :sef
					");
					
					$stmt_url->execute(array('url' => 'ads/section/'.$id, 'sef' => $sef));
				}
				else
				{
					$stmt_url = $db->prepare("
					UPDATE url SET
					sef = :sef
					WHERE url = :url
					LIMIT 1
					");
					
					$stmt_url->execute(array('url' => 'ads/section/'.$id, 'sef' => $sef));
				}
			}
		}
	}
	else
	{
		$stmt_delete = $db->prepare("DELETE FROM url WHERE url = :url LIMIT 1");
		$stmt_delete->execute(array('url' => 'ads/section/'.$id));
	}

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/ads/section/edit/".$id); exit;}
}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
