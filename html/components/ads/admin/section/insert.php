<?php
defined('AUTH') or die('Restricted access');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/ads/admin/section/image_resize.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/components/ads/classes/AdsSection.php');

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

	if(!empty($file_name)) $image = 1;
	else $image = 0;

	// Вставляем в таблицу "com_ads_section"	
	$stmt_section = $db->prepare("
	INSERT INTO com_ads_section SET
	title = :title,
	image = :image,
	pub = :pub,
	options = :options
	");
	
	$stmt_section->execute(array(
	'title' => $title,
	'image' => $image,
	'pub' => $pub,
	'options' => $options,
	));
	
	$section_id = $db->lastInsertId();

	// Обработка изображения
	ads_image_resize($section_id);
	
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
	component = 'ads',
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
	'pub' => $pub,
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
				$stmt_url->execute(array('url' => 'ads/section/'.$section_id));
				
				if($stmt_url->rowCount() == 0) // нет такого `url`
				{
					$stmt_url = $db->prepare("
					INSERT INTO url SET
					url = :url,
					sef = :sef
					");
					
					$stmt_url->execute(array('url' => 'ads/section/'.$section_id, 'sef' => $sef));
				}
			}
		}
	}

	if($bt_save == 'Сохранить'){Header ("Location: /admin"); exit;}
	else {Header ("Location: /admin/com/ads/section/edit/".$section_id); exit;}
}

// ==================================================================================

function a_com()
{
	global $err;
	echo $err;

} // конец функции

?>
