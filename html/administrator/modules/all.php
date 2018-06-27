<?php
// Выводит модули сайта в центре (компонентом)
defined('AUTH') or die('Restricted access');

$head->addFile('/administrator/modules/style.css');

function a_com()
{
	global $root, $db, $domain;

	$out = '';

	// вывод модулей
	$stmt_mod = $db->query("SELECT * FROM modules WHERE enabled = '1' ORDER BY block, ordering ASC ");

	while($m = $stmt_mod->fetch())
	{
		$modules_block = $m['block'];

		switch ($m['pub']) {
		case 1:
			$pub_x = '<img border="0" src="/administrator/modules/images/all.png" width="35" height="35" title="Показывать везде">';
			$classmenu = "menu_pub";
			break;
		case 2:
			$pub_x = '<img border="0" src="/administrator/modules/images/desctop.png" width="35" height="35" title="Показывать только на компютнрах">';
			$classmenu = "menu_pub";
			break;				
		case 3:
			$pub_x = '<img border="0" src="/administrator/modules/images/mobile.png" width="35" height="35" title="Показывать только на телефонах">';
			$classmenu = "menu_pub";
			break;				
		default:
			$pub_x = '<img border="0" src="/administrator/modules/images/no.png" width="35" height="35" title="Не показывать">';
			$classmenu = "menu_unpub";	
		}

		// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
		$block_description = "";

		$stmt_block = $db->prepare("SELECT description FROM block WHERE block = :block");
		$stmt_block->execute(array('block' => $modules_block));
		$block_description = $stmt_block->fetchColumn();
		// ======== / загрузка блоков вывода =======

		// Подключаем контекстное меню
		if(file_exists($root.'/modules/'.$m['module'].'/admin/context_menu.php'))
		{
			include_once($root.'/modules/'.$m['module'].'/admin/context_menu.php');
		}
		

		
		$out .= '
			<tr>
				<td class="mod_link '.$m['module'].'_ico">&nbsp;</td>
				<td class="contextmenu_module_'.$m['module'].'" data-id="'.$m['id'].'">
					<a class="'.$classmenu.'" href="/admin/modules/'.$m['module'].'/'.$m['id'].'">'.$m['title'].'</a>
				</td>
				<td '.$classmenu.'"><a class="'.$classmenu.'" href="/admin/modules/'.$m['module'].'/'.$m['id'].'">'.$m['description'].'</a></td>
				<td>'.$block_description.'</td>
				<td>'.$m['ordering'].'</td>
				<td>'.$pub_x.'</td>
			</tr>
			';

	}


	echo '
		<div class="container">
			<h1 class="modules_h1">Модули сайта</h1><a class="modules_add" href="/admin/modules/add">+ Добавить модуль</a>
			<table class="admin_table even_odd">
				<tr>
					<th style="width:50px;" title="Поставьте галочку, если хотите совершить действие над этим пунктом"></td>
					<th style="width:250px;">МОДУЛИ САЙТА</td>
					<th>Описание модулей</td>
					<th style="width:200px;">Блок вывода</td>
					<th style="width:100px;" title="Порядок следования модуля в блоке" style="text-align:center">Порядок</td>
					<th style="width:70px;" title="Состояние показа модуля" style="text-align:center">Показ</td>
				</tr>
				'.$out.'
			</table>
		</div>
	';

} // конец функции компонента
?>