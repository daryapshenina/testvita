<?php
// DAN 2014
// выводит пункты верхнего меню.
defined('AUTH') or die('Restricted access');

if (isset($d[0]) && $d[0] != "") {$prm_0 = '/'.$d[0];} else {$prm_0 = '';}
if (isset($d[1]) && $d[1] != "") {$prm_1 = '/'.$d[1];} else {$prm_1 = '';}
if (isset($d[2]) && $d[2] != "") {$prm_2 = '/'.$d[2];} else {$prm_2 = '';}
if (isset($d[3]) && $d[3] != "") {$prm_3 = '/'.$d[3];} else {$prm_3 = '';}
if (isset($d[4]) && $d[4] != "") {$prm_4 = '/'.$d[4];} else {$prm_4 = '';}
if (isset($d[5]) && $d[5] != "") {$prm_5 = '/'.$d[5];} else {$prm_5 = '';}

$prm_topmenu = $prm_0.$prm_1.$prm_2.$prm_3.$prm_4.$prm_5;

// ======= Подключение javascript - если существуют родительские пункты и они опубликованы =======
$parent_query = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = 'top' AND `parent` != '0' AND `pub` = '1'") or die ("Невозможно сделать выборку из таблицы - 1");

$parent_result = mysql_num_rows($parent_query);


echo '
	<script type="text/javascript">
	function topmenu_ico()
	{
		topmenu_wrap = document.getElementById("topmenu_wrap");

		if(topmenu_wrap.className == "")
		{
			topmenu_wrap.className = "animation_show topmenu_display";
		}
		else
		{
			topmenu_wrap.className = "";
		}
	}
	</script>
';


if ($parent_result > 0)
{
	$menutop_sub_event = 'onmouseover="menutop_2(this, \'over\');" onmouseout="menutop_2(this, \'out\');" ';

	echo '
		<script type="text/javascript">

		function menutop_2 (parent_block, ev)
		{
			if(ev ==\'over\')
			{
				parent_block.childNodes[1].style.display="block";
			}
			if(ev ==\'out\')
			{
				parent_block.childNodes[1].style.display="none";
			}
		}
		
		</script>
	';
}
else
{
	$menutop_sub_event = '';
}
// ======= / Подключение javascript - если существуют родительские пункты и они опубликованы =======

// --- Открываем таблицу ---
echo '
	<div id="topmenu_ico" onclick="topmenu_ico();" style="width:48px;height:48px; background:url(/modules/topmenu/frontend/topmenu.png); display:none; cursor:pointer;"></div>
	<div id="topmenu_wrap">
	<table id="topmenu_tab" class="tab">
		<tr>
			<td class="topmenu-0"></td>
	';
// --- / Открываем таблицу ---


// ======= Первый уровень =======
$level_1_query = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = 'top' AND `parent` = '0' AND `pub` = '1'  ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 1");

$level_1_result = mysql_num_rows($level_1_query);

if ($level_1_result > 0)
{
	while($level_1 = mysql_fetch_array($level_1_query)):
		$level_1_id = $level_1['id'];
		$level_1_name = $level_1['name'];
		$level_1_component = $level_1['component'];
		$level_1_p1 = $level_1['p1'];
		$level_1_p2 = $level_1['p2'];
		$level_1_p3 = $level_1['p3'];
		$level_1_id_com = $level_1['id_com'];

		if ($level_1_component != ''){$level_1_p0 = '/'.$level_1_component;}
		if ($level_1_p1 != ''){$level_1_p1 = '/'.$level_1_p1;} else{$level_1_p1 = '';}
		if ($level_1_p2 != ''){$level_1_p2 = '/'.$level_1_p2;} else{$level_1_p2 = '';}
		if ($level_1_p3 != ''){$level_1_p3 = '/'.$level_1_p3;} else{$level_1_p3 = '';}
		if ($level_1_id_com != ''){$level_1_p4 = '/'.$level_1_id_com;}

		$level_1_p = $level_1_p0.$level_1_p1.$level_1_p2.$level_1_p3.$level_1_p4;

		// убираем один символ / в начале (для чпу url)
		$p_qs_t_1 = substr($level_1_p, 1);

		if ($prm_topmenu == $level_1_p || ($prm_topmenu == "" && $level_1_p == "/page/1"))
		{
			$class_am = 'activetopmenu';
		}
		else
		{
			$am = "";
			$class_am = 'topmenu';
		}


		// ======= Проверяем, есть ли подменю у этого пункта =========
		$level_2_query = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = 'top' AND `parent` = '$level_1_id' AND `pub` = '1'  ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 1");

		$level_2_result = mysql_num_rows($level_2_query);

		// Если у меню 1-го уровня есть п/п - выводим javascript обработчики; иначе ничего не выводим
		if ($level_2_result > 0){$mt_sub_1_event = $menutop_sub_event;} else {$mt_sub_1_event = "";}
		// ======= / Проверяем, есть ли подменю у этого пункта =======


		// ======= Выводим первый уровень =======
		// проверяем, является ли данный пункт меню главным пунктом меню страницы
		if ($level_1_component == "page" && $level_1_id_com == "1")
		{
			echo '
				<td class="'.$class_am.'-1"></td>
				<td class="'.$class_am.'-2"><div  '.$mt_sub_1_event.' class="'.$class_am.'-2"><div><a class="'.$class_am.'" href="/">'.$level_1_name.'</a></div>';

			// второй уровень
			topmenu_2($level_1_id, $level_2_query, $level_2_result, $menutop_sub_event);

			echo '
				</div></td>
				<td class="'.$class_am.'-3"></td>
			';
		}
		// вывод обычных пунктов меню
		else
		{
			// если есть в массиве ЧПУ - заменяем
			if(isset($url_arr[$p_qs_t_1]) && $url_arr[$p_qs_t_1] != '')
			{
				$level_1_p = '/'.$url_arr[$p_qs_t_1];
			}

			echo '
				<td class="'.$class_am.'-1"></td>
				<td '.$mt_sub_1_event.' class="'.$class_am.'-2"><div><a class="'.$class_am.'" href="'.$level_1_p.'">'.$level_1_name.'</a></div>';

			// второй уровень
			topmenu_2($level_1_id, $level_2_query, $level_2_result, $menutop_sub_event);

			echo '
				</td>
				<td class="'.$class_am.'-3"></td>
			';
		}
		// ======= / Выводим первый уровень =======



	endwhile;
} // конец проверки $level_1_result > 0
// ======= / Первый уровень =======


// ------- Закрываем таблицу -------
echo'
			<td class="topmenu-4"></td>
		</tr>
	</table>
	</div>
';
// ------- / Закрываем таблицу --------




// ===== Второй уровень ===============================================================================
// $level_1_id - id подпункта меню первого уровня
// $level_2_query - QUERY запрос к БД
// $level_2_result - количество пунктов в подуровне у $level_1_id
// $menutop_sub_event - событие вызова подменю
function topmenu_2($level_1_id, $level_2_query, $level_2_result, $menutop_sub_event) // $i = 0 начальный уровень меню, $level_1_lvl - уровень меню
{
	global $site, $url_arr, $prm_topmenu;

	if ($level_2_result > 0)
	{
		echo '<div class="menutop_lvl_2_block">';

		while($level_2 = mysql_fetch_array($level_2_query)):
			$level_2_id = $level_2['id'];
			$level_2_name = $level_2['name'];
			$level_2_component = $level_2['component'];
			$level_2_p1 = $level_2['p1'];
			$level_2_p2 = $level_2['p2'];
			$level_2_p3 = $level_2['p3'];
			$level_2_id_com = $level_2['id_com'];

			if ($level_2_component != ''){$level_2_p0 = '/'.$level_2_component;}
			if ($level_2_p1 != ''){$level_2_p1 = '/'.$level_2_p1;} else{$level_2_p1 = '';}
			if ($level_2_p2 != ''){$level_2_p2 = '/'.$level_2_p2;} else{$level_2_p2 = '';}
			if ($level_2_p3 != ''){$level_2_p3 = '/'.$level_2_p3;} else{$level_2_p3 = '';}
			if ($level_2_id_com != ''){$level_2_p4 = '/'.$level_2_id_com;}

			$level_2_p = $level_2_p0.$level_2_p1.$level_2_p2.$level_2_p3.$level_2_p4;

			// убираем один символ / в начале (для чпу url)
			$p_qs_t_2 = substr($level_2_p, 1);

			// если есть в массиве ЧПУ - заменяем
			if(isset($url_arr[$p_qs_t_2]) && $url_arr[$p_qs_t_2] != '')
			{
				$level_2_p = '/'.$url_arr[$p_qs_t_2];
			}

			// ======= Проверяем, есть ли подменю у этого пункта =========
			$level_3_query = mysql_query("SELECT * FROM `menu` WHERE `menu_type` = 'top' AND `parent` = '$level_2_id' AND `pub` = '1'  ORDER BY `ordering` ASC") or die ("Невозможно сделать выборку из таблицы - 1");

			$level_3_result = mysql_num_rows($level_3_query);

			// Если у меню 1-го уровня есть п/п - выводим javascript обработчики; иначе ничего не выводим
			if ($level_3_result > 0){$mt_sub_2_event = $menutop_sub_event;} else {$mt_sub_2_event = "";}
			// ======= / Проверяем, есть ли подменю у этого пункта =======

			// Если у меню 2-го уровня есть п/п - выводим javascript обработчики; иначе ничего не выводим
			if ($level_3_result > 0){$mt_sub_2_event = $menutop_sub_event;} else {$mt_sub_2_event = "";}


			// ======= Выводим первый уровень =======
			echo '<div '.$mt_sub_2_event.' class="topmenu-2-parent"><div><a class="menutop_sub" href="'.$level_2_p.'">'.$level_2_name.'</a></div>';

			// вызываем третий уровень
			topmenu_3($level_2_id, $level_3_query, $level_3_result);

			echo '</div>';
			// ======= / Выводим первый уровень =======


		endwhile;

		echo '</div>';

	} // конец проверки $level_2_result > 0
} // конец функции topmenu_2
// ======= / Второй уровень =============================================================================


// ===== Третий уровень =================================================================================
function topmenu_3($level_2_id, $level_3_query, $level_3_result) // $i = 0 начальный уровень меню, $level_1_lvl - уровень меню
{
	global $site, $url_arr, $prm_topmenu, $menutop_sub_event;

	// выводим 3-й уровень
	if ($level_3_result > 0)
	{
		echo '<div class="menutop_lvl_3_block">';

		while($level_3 = mysql_fetch_array($level_3_query)):
			$level_3_id = $level_3['id'];
			$level_3_name = $level_3['name'];
			$level_3_component = $level_3['component'];
			$level_3_p1 = $level_3['p1'];
			$level_3_p2 = $level_3['p2'];
			$level_3_p3 = $level_3['p3'];
			$level_3_id_com = $level_3['id_com'];

			if ($level_3_component != ''){$level_3_p0 = '/'.$level_3_component;}
			if ($level_3_p1 != ''){$level_3_p1 = '/'.$level_3_p1;} else{$level_3_p1 = '';}
			if ($level_3_p2 != ''){$level_3_p2 = '/'.$level_3_p2;} else{$level_3_p2 = '';}
			if ($level_3_p3 != ''){$level_3_p3 = '/'.$level_3_p3;} else{$level_3_p3 = '';}
			if ($level_3_id_com != ''){$level_3_p4 = '/'.$level_3_id_com;}

			$level_3_p = $level_3_p0.$level_3_p1.$level_3_p2.$level_3_p3.$level_3_p4;

			// убираем один символ / в начале (для чпу url)
			$p_qs_t_3 = substr($level_3_p, 1);

			// если есть в массиве ЧПУ - заменяем
			if(isset($url_arr[$p_qs_t_3]) && $url_arr[$p_qs_t_3] != '')
			{
				$level_3_p = '/'.$url_arr[$p_qs_t_3];
			}

			echo '<a class="menutop_sub" href="'.$level_3_p.'">'.$level_3_name.'</a>';

		endwhile;

		echo '</div>';

	} // конец проверки $level_3_result > 0
} // конец функции topmenu_3
// ======= / Третий уровень =============================================================================

?>
