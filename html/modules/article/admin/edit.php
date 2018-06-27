<?php
defined('AUTH') or die('Restricted access');

$mod_id  = intval($d[3]);

$head->addFile('/modules/icon/admin/tmp.js');

include_once($root.'/administrator/modules/classes/Admin.php');
$module = new ModulesAdmin($mod_id);

if(isset($_POST["title"])){$mod_title = htmlspecialchars($_POST["title"]);} else {$mod_title = '';}
if(isset($_POST["pub"])){$mod_pub = intval($_POST["pub"]);} else {$mod_pub = 0;}
if(isset($_POST["titlepub"])){$mod_titlepub = intval($_POST["titlepub"]);} else {$mod_titlepub = 0;}
if(isset($_POST["block"])){$mod_block = htmlspecialchars($_POST["block"]);} else {$mod_block = '';}
if(isset($_POST["ordering"])){$mod_ordering = intval($_POST["ordering"]);} else {$mod_ordering = 0;}
if(isset($_POST["type_out"])){$mod_vivodchecked = intval($_POST["type_out"]);} else {$mod_vivodchecked = 0;}
if(isset($_POST["kolvostat"])){$mod_kolvostatval = intval($_POST["kolvostat"]);} else {$mod_kolvostatval = 0;}
if(isset($_POST["selecttype"])){$mod_selecttype = intval($_POST["selecttype"]);} else {$mod_selecttype = 0;}
if(isset($_POST["section_link"])){$mod_section_link = intval($_POST["section_link"]);} else {$mod_section_link = 0;}
if(isset($_POST["anchor"])){$mod_anchor = htmlspecialchars($_POST["anchor"]);} else {$mod_anchor = '';}

// Получаем массив из категорий
if (isset($_POST["razdel"]))
{
	$mod_razdel = $_POST["razdel"];

	// проверяем есть ли что нибудь в массиве
	$c = 0;
	$mod_razdel_save_bd = '';
	if (!empty($mod_razdel))
	{
		foreach ($mod_razdel as $value)
		{
			$c++;
			if ($c == 1){$mod_razdel_save_bd .= $value;} else{$mod_razdel_save_bd .= ";".$value;}
		}
	}
	else
	{
		$mod_razdel_save_bd = '0';
	}
}
else
{
	$mod_razdel_save_bd = '0';
}



function a_com()
{
	global $db, $domain, $module, $module, $mod_id;
	$m = $module->get_array();

	// Настройка вывода модуля статей -> новости / случайная
	if ($m['p1'] == '1')
	{
		$type_out_1 = '';
		$type_out_2 = 'checked="checked"';
	}
	else
	{
		$type_out_1 = 'checked="checked"';
		$type_out_2 = '';
	}

	//
	if ($m['p4'] == '1')
	{
		$view_out_1 = '';
		$view_out_2 = 'checked="checked"';
	}
	else
	{
		$view_out_1 = 'checked="checked"';
		$view_out_2 = '';
	}

	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{
		// запускаем цикл вывода категорий
		// подключаемся к бд для вывода заголовков категорий

		$section_query = $db->query("SELECT id, title FROM com_article_section");

		$i = 0;
		$chb = '';
		$rb = '';

		// разделы, указанные в меню.
		$razdel_checked_array = explode(";", $m['p2']);

		while($cn = $section_query->fetch())
		{
			$razdel_checked_[$cn['id']] = '';

			foreach ($razdel_checked_array as $value)
			{
				if ($cn['id'] == $value){$razdel_checked_[$cn['id']] = 'checked="checked"';}
			}

			// чекбоксы категорий
			$chb .= '<input type="checkbox" name="sections['.$i.']" value="'.$cn['id'].'" '.$razdel_checked_[$cn['id']].' /> <span>'.$cn['title'].'</span><br />';

			// если не существует линк на раздел
			if (!isset($m['p5']) || $m['p5'] == '' || $m['p5'] == 0)
			{
				$section_link_ots = 'checked';
			}
			else {$section_link_ots = '';}

			if ($cn['id'] == $m['p5'])
			{
				// радиокнопки категорий - checked
				$rb .= '<input id="section_link_'.$cn['id'].'" class="input" type="radio" name="section_link" value="'.$cn['id'].'" checked /> <label for="section_link_'.$cn['id'].'">'.$cn['title'].'</label><br />';
			}
			else
			{
				// радиокнопки категорий
				$rb .= '<input id="section_link_'.$cn['id'].'" class="input" type="radio" name="section_link" value="'.$cn['id'].'" /> <label for="section_link_'.$cn['id'].'">'.$cn['title'].'</label><br />';
			}


			$i++;
		}


		echo '
		<div class="container">
			<h1><img border="0" src="/modules/article/admin/images/ico.png" style="float:left; margin-right:10px;" />Модуль "Статей"</h1>

			<form method="POST" action="/admin/modules/article/update/'.$m['id'].'">

			<table class="admin_table_2">
				'.$module->description().'
				'.$module->pub().'
				'.$module->title().'
				'.$module->block().'
				'.$module->order().'
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Вывод</td>
					<td>
						<input id="type_out_1" type="radio" class="input" name="type" value="0" '.$type_out_1.' /><label for="type_out_1">Случайная статья</label><br />
						<input id="type_out_2" type="radio" class="input" name="type" value="1" '.$type_out_2.' /><label for="type_out_2">Последняя статья</label>
					</td>
				</tr>
				<tr>
					<td width="200" height="25"></td>
					<td></td>
				</tr>
				<tr>
					<td width="200" height="25" style="vertical-align:middle;">Тип вывода</td>
					<td>
						<input id="view_out_1" type="radio" class="input" name="view" value="0" '.$view_out_1.' /><label for="view_out_1">Только заголовок</label> <br />
						<input id="view_out_2" type="radio" class="input" name="view" value="1" '.$view_out_2.' /><label for="view_out_2">Заголовок и вводный текст</label>
					</td>
				</tr>
				<tr>
					<td width="200" height="25"></td>
					<td></td>
				</tr>
				<tr>
					<td width="200" height="25" style="vertical-align:middle;">Кол-во статей</td>
					<td><input class="input" type="number" min="0" max="100" required type="text" name="num_articles" size="1" maxlength="3" value="'.$m['p3'].'"></td>
				</tr>
				<tr>
					<td width="200" height="25"></td>
					<td></td>
				</tr>
				<tr>
					<td width="200" height="25" style="vertical-align:middle;">Разделы</td>
					<td>
					'.$chb.'
					</td>
				</tr>
				<tr>
					<td width="200" height="25"></td>
					<td></td>
				</tr>
				<tr>
					<td width="200" height="25" style="vertical-align:middle;">Ссылка внизу модуля на раздел</td>
					<td>
						<input id="section_link_0" type="radio" class="input" name="section_link" value="0" '.$section_link_ots.' /> <label for="section_link_0">Отсутствует</label> <br />
						'.$rb.'
					</td>
				</tr>
				<tr>
					<td width="200" height="25"></td>
					<td></td>
				</tr>
				<tr>
					<td width="200" height="25">Текст ссылки</td>
					<td><input class="input" type="text" name="anchor" size="20" value="'.$m['p6'].'"></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<div class="seo_fon">
				<div class="accordion_head left_head">'.LANG_M_SIZE.'</div>
				<div class="accordion_body mod_icon_ab4">
					<div>&nbsp;</div>
					<table class="admin_table_2">
						'.$module->width_prc($m['p8']).'
						'.$module->margin($m['p9']).'
						'.$module->padding($m['p10']).'
					</table>
					<div>&nbsp;</div>
				</div>
			</div>
			<br>
			<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
			<br>
			&nbsp;
			</form>
		</div>
		<script type="text/javascript">
			DAN.accordion("accordion_head", "accordion_body");
		</script>
		';
	} // конец проверки 'enabled'
	else
	{
		echo '<div id="main-top">Модуль "article" не подключён</div>';
	}
} // конец функции


?>