<?php
defined('AUTH') or die('Restricted access');

include_once($root.'/administrator/modules/classes/Admin.php');

$module = new ModulesAdmin($d[3]);

function a_com()
{
	global $db, $domain, $d, $module;

	// вывод содержимого модуля
	$stmt_mod = $db->prepare("SELECT * FROM modules WHERE module = 'shop' AND id = :id");
	$stmt_mod->execute(array('id' => $d[3]));
	
	while($m = $stmt_mod->fetch())
	{
		$module_id = $m['id'];
		$module_title = $m['title'];
		$module_pub = $m['pub'];
		$module_titlepub = $m['titlepub'];
		$module_enabled = $m['enabled'];
		$module_description = $m['description'];
		$module_block = $m['block'];
		$module_ordering = $m['ordering'];
		$module_vivodchecked = $m['p1'];
		$module_kolvotov = $m['p2'];
		$module_shop_section = $m['p3'];
		$module_razdel_link = $m['p4'];
		$module_link_name = $m['p5'];
		$moduledit_modee = $m['p6'];
		$module_link_position = $m['p7'];
		if($m['p10'] == 1){$type_selected_1 = 'selected';$type_selected_0 = '';}else{$type_selected_0 = 'selected';$type_selected_1 = '';}
	}

	$block_option = '';

	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	$stmt_block = $db->query("SELECT * FROM block");
	
	while($b = $stmt_block->fetch())
	{
		if ($b['block'] == $module_block){$selected = 'selected';} else {$selected = '';}
		$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';		
	}
	// ======== / загрузка блоков вывода =======


	// настройка вывода модуля товаров
	if($module_vivodchecked == '1')
	{
		$vivodchecked1 = '';
		$vivodchecked2 = 'checked="checked"';
	}
	else
	{
		$vivodchecked1 = 'checked="checked"';
		$vivodchecked2 = '';
	}


	// создаем массив из строки
	$module_shop_section_arr = explode(";", $module_shop_section);		
	
	// подключаемся к бд для вывода заголовков категорий
	$stmt_section = $db->query("SELECT id, title FROM com_shop_section WHERE pub = '1'");

	while($ch = $stmt_section->fetch())
	{
		foreach ($module_shop_section_arr as $value)
		{
			if ($ch['id'] == $value)
			{
				$razdel_checked_[$ch['id']] = 'checked="checked"';
			}
		}			
	}

	// Мод
	list($mode_checked_0, $mode_checked_1, $mode_checked_2, $mode_checked_3, $mode_checked_4) = '';		
	
	switch($moduledit_modee)
	{
		case 0:{$mode_checked_0 = 'selected="selected"';} break;
		case 1:{$mode_checked_1 = 'selected="selected"';} break;
		case 2:{$mode_checked_2 = 'selected="selected"';} break;
		case 3:{$mode_checked_3 = 'selected="selected"';} break;	
		case 4:{$mode_checked_4 = 'selected="selected"';} break;
	}

	// устанавливаем признак публикации
	list($pub_0, $pub_1, $pub_2, $pub_3) = '';
	
	switch($module_pub)
	{
		case 0:
			$pub_0 = 'selected="selected"';
			break;

		case 1:
			$pub_1 = 'selected="selected"';
			break;

		case 2:
			$pub_2 = 'selected="selected"';
			break;

		case 3:
			$pub_3 = 'selected="selected"';
			break;
	}

	// устанавливаем признак публикации заголовка модуля
	if ($module_titlepub == 1){$titlepub = "checked";} else{$titlepub = "";}

	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($module_enabled == "1")
	{
		// запускаем цикл вывода категорий
		// подключаемся к бд для вывода заголовков категорий
		$query_section = $db->query("SELECT id, title FROM com_shop_section");

		$chb = '';
		$i = 0;
		$rb ='';

		while($cn = $query_section->fetch())
		{
			if(!isset($razdel_checked_[$cn['id']])){$razdel_checked_[$cn['id']] = '';}
			// чекбоксы категорий
			$chb .= '<input type="checkbox" name="prazdel['.$i.']" value="'.$cn['id'].'" '.$razdel_checked_[$cn['id']].' /> <span>'.$cn['title'].'</span><br />';

			// если не существует линк на раздел
			if (!isset($razdel_link) || $razdel_link == '' || $razdel_link == 0)
			{
				$razdel_link_ots = 'checked';
			}

			if ($cn['id'] == $module_razdel_link)
			{
				// радиокнопки категорий - checked
				$rb .= '<input type="radio" name="section_link" value="'.$cn['id'].'" checked /> <span>'.$cn['title'].'</span><br />';
			}		
			else
			{
				// радиокнопки категорий
				$rb .= '<input type="radio" name="section_link" value="'.$cn['id'].'" /> <span>'.$cn['title'].'</span><br />';
			}


			$i++;
		}
		
		list($discount_link_check, $new_link_check, $hit_link_check) = '';
		if($module_razdel_link == 'discount'){$discount_link_check = 'checked';}
		if($module_razdel_link == 'new'){$new_link_check = 'checked';}
		if($module_razdel_link == 'hit'){$hit_link_check = 'checked';}	
		
		list($link_position_0, $link_position_1) = '';
		if($module_link_position == '1'){$link_position_1 = 'checked';}else {$link_position_0 = 'checked';}

		echo '
		<h1><img border="0" src="/modules/shop/admin/images/ico.png" style="width:25; height:25px; float:left; padding-top:2px;"/>&nbsp;&nbsp;'.LANG_M_SHOP_MAIN_TITLE.'</h1>

		<form method="POST" action="/admin/modules/shop/update/'.$module_id.'">

		<table class="admin_table_2">
			'.$module->pub().'
			'.$module->title().'
			'.$module->description().'
			'.$module->block().'
			'.$module->order().'
			<tr>
				<td>Тип модуля</td>
				<td>
					<select class="input" name="type">
						<option value="0" '.$type_selected_0.'>Стандартный</option>
						<option value="1" '.$type_selected_1.'>Скроллер</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width:170px;">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>'.LANG_M_SHOP_DISPLAY.'</td>
				<td>
					<input type="radio" name="ptype_vivod" value="0" '.$vivodchecked1.' /> <span>'.LANG_M_SHOP_RANDOM_ITEM.'</span> <br />
					<input type="radio" name="ptype_vivod" value="1" '.$vivodchecked2.' /> <span>'.LANG_M_SHOP_LAST_ITEM.'</span>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>'.LANG_M_SHOP_NUMBER_OF_ITEMS.'</td>
				<td><input class="input" type="number" min="1" max="1000" name="pkoltov" size="1" maxlength="3" value="'.$module_kolvotov.'" required ></td>
			</tr>
			<tr>
				<td>'.LANG_M_SHOP_VIEW_ITEM.':</td>
				<td>
					<select class="input" name="mode">
						<option value="0" '.$mode_checked_0.'>'.LANG_M_SHOP_VIEW_ITEM_ALL.'</option>
						<option value="1" '.$mode_checked_1.'>'.LANG_M_SHOP_VIEW_ITEM_ONLY_SALE.'</option>
						<option value="2" '.$mode_checked_2.'>'.LANG_M_SHOP_VIEW_ITEM_ONLY_NEW.'</option>
						<option value="3" '.$mode_checked_3.'>'.LANG_M_SHOP_VIEW_ITEM_SALE_AND_NEW.'</option>
						<option value="4" '.$mode_checked_4.'>'.LANG_M_SHOP_VIEW_ITEM_ONLY_HIT.'</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><b>'.LANG_M_SHOP_SECTIONS.'</b><br />'.LANG_M_SHOP_IF_NOT_SELECTED.'<br /><br /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>'.$chb.'</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><b>'.LANG_M_SHOP_LINK_ON_SECTION.'</b><br /><br /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="radio" name="section_link" value="0" '.$razdel_link_ots.' /> <b>'.LANG_M_SHOP_LINK_OFF.'</b><br>
					<input type="radio" name="section_link" value="discount" '.$discount_link_check.' /> <b>'.LANG_M_SHOP_LINK_DISCOUNT.'</b><br>
					<input type="radio" name="section_link" value="new" '.$new_link_check.' /> <b>'.LANG_M_SHOP_LINK_NEW.'</b><br>
					<input type="radio" name="section_link" value="hit" '.$hit_link_check.' /> <b>'.LANG_M_SHOP_LINK_HIT.'</b><br>								
					'.$rb.'
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>'.LANG_M_SHOP_LINK_POSITION.'</td>
				<td>
					<input type="radio" name="link_position" value="1" '.$link_position_1.' /> '.LANG_M_SHOP_LINK_POSITION_TITLE.'<br>
					<input type="radio" name="link_position" value="0" '.$link_position_0.' /> '.LANG_M_SHOP_LINK_POSITION_BOTTOM.'<br>
				</td>
			</tr>				
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>				
			<tr>
				<td>'.LANG_M_SHOP_TEXT_OF_LINK.'</td>
				<td><input class="input" type="text" name="link_name" size="20" value="'.$module_link_name.'"></td>
			</tr>
		</table>
		<br>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="'.LANG_M_SHOP_SAVE.'" name="bt_save">&nbsp;<input class="graybutton" type="submit" value="'.LANG_M_SHOP_ACCEPT.'" name="bt_prim">&nbsp;<input class="redbutton" type="submit" value="'.LANG_M_SHOP_CANCEL.'" name="bt_none">
		<br>
		<br>
		&nbsp;
		</form>
		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">'.LANG_M_SHOP_MODULE_OFF.'</div>';
	}
}


?>
