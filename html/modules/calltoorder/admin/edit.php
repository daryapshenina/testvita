<?php
defined('AUTH') or die('Restricted access');

// id модуля
$mod_id  = intval($d[3]);

function a_com()
{ 
	global $db, $domain, $mod_id;

	$stmt_modules = $db->prepare("SELECT * FROM modules WHERE id = :id");
	$stmt_modules->execute(array('id' => $mod_id));

	$m = $stmt_modules->fetch();

	// ======= ЗАГРУЗКА БЛОКОВ ВЫВОДА ========
	$block_query = $db->query("SELECT * FROM block");

	$block_option = '';
	if($block_query->rowCount() > 0)
	{
		while($b = $block_query->fetch())
		{
			if ($b['block'] == $m['block']){$selected = 'selected';} else {$selected = '';}
			$block_option .= '<option '.$selected.' value='.$b['block'].'>'.$b['description'].'</option>';				
		}
	}	
		
	// устанавливаем признак публикации
	$pub_0 = $pub_1 = $pub_2 = $pub_3 = '';

	switch($m['pub'])
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

	// вид отображения модуля
	if ($m['p1'] == 0){$view_select_0 = "selected"; $view_select_1 = '';} else{$view_select_0 = ''; $view_select_1 = "selected";}	

	// подключаем модуль только в том случае, если стоит признак 'enabled' (подключён к этому сайту)
	if ($m['enabled'] == "1")
	{
		echo '
		<style type="text/css">
		#mod_calltoorder_circle {
			z-index					:1100;
			position				:fixed;
			opacity					:0.8;
			right					:100px;
			bottom					:100px;
			border-radius			:100%;
			display					:none;
		}

		.mod_calltoorder_circle {
			z-index					:1130;
			top						:25%;
			left					:25%;
			position				:absolute;
			border-radius			:100%;
			background-image		:url(/modules/calltoorder/frontend/images/phone.png);
			background-repeat		:no-repeat;
			background-position		:center;
			background-size			:50%;
		}

		.mod_calltoorder_circle_wave_out {
			z-index					:1120;
			top						:25%;
			left					:25%;
			position				:absolute;
			border-radius			:100%;
			-webkit-animation		:mod_calltoorder_wave_out 2s infinite linear;
			animation				:mod_calltoorder_wave_out 2s infinite linear;
		}

		.mod_calltoorder_circle_wave_in {
			z-index					:1110;
			top						:25%;
			left					:25%;
			position				:absolute;
			border-style			:solid;
			border-width			:1px;
			border-radius			:100%;
			-webkit-animation		:mod_calltoorder_wave_in 2s infinite linear;
			animation				:mod_calltoorder_wave_in 2s infinite linear;
		}

		@-webkit-keyframes mod_calltoorder_wave_out {
			0%{
				-webkit-transform		:scale(1);
				-webkit-opacity			:0.5;
			}
			
			100%{
				-webkit-transform		:scale(2);
				-webkit-opacity			:0;
			}
		}

		@keyframes mod_calltoorder_wave_out {
			0%{
				transform				:scale(1);
				opacity					:0.5;
			}
			
			100%{
				transform				:scale(2);
				opacity					:0;
			}
		}

		@-webkit-keyframes mod_calltoorder_wave_in {
			0%{
				-webkit-transform		:scale(2);
				-webkit-opacity			:0;		
			}
			
			100%{
				-webkit-transform		:scale(1);
				-webkit-opacity			:0.5;		
			}
		}

		@keyframes mod_calltoorder_wave_in {
			0%{
				transform				:scale(2);
				opacity					:0;	
			}
			
			100%{
				transform				:scale(1);
				opacity					:0.5;
			}
		}
		</style>
		
		<script type="text/javascript">
		function view_type()
		{
			var v = document.getElementById("view").value;
			if(v == 1)
			{
				document.getElementById("tr_size").style.display = "table-row";
				document.getElementById("tr_right").style.display = "table-row";
				document.getElementById("tr_bottom").style.display = "table-row";					
				document.getElementById("tr_color").style.display = "table-row";
				document.getElementById("mod_calltoorder_circle").style.display = "block";					
			}
			else
			{
				document.getElementById("tr_size").style.display = "none";
				document.getElementById("tr_right").style.display = "none";
				document.getElementById("tr_bottom").style.display = "none";					
				document.getElementById("tr_color").style.display = "none";
				document.getElementById("mod_calltoorder_circle").style.display = "none";					
			}
		}
		
		function size_value()
		{
			var s = document.getElementById("size").value;
			document.getElementById("size_out").innerHTML = s;	
			document.getElementById("mod_calltoorder_circle").style.height = s * 2 + "px";
			document.getElementById("mod_calltoorder_circle").style.width = s * 2 + "px";	
			document.getElementById("circle").style.height = s + "px";
			document.getElementById("circle").style.width = s + "px";
			document.getElementById("wave_out").style.height = s + "px";
			document.getElementById("wave_out").style.width = s + "px";
			document.getElementById("wave_in").style.height = s - 2 + "px";
			document.getElementById("wave_in").style.width = s - 2 + "px";	
		}
		
		function right_value()
		{
			var r = document.getElementById("c_right").value;
			document.getElementById("right_out").innerHTML = r;
			document.getElementById("mod_calltoorder_circle").style.right = r + "px";			
		}

		function bottom_value()
		{
			var b = document.getElementById("c_bottom").value;
			document.getElementById("bottom_out").innerHTML = b;
			document.getElementById("mod_calltoorder_circle").style.bottom = b + "px";			
		}

		function color_value()
		{
			var c = document.getElementById("color").value;

			document.getElementById("circle").style.backgroundColor = c;
			document.getElementById("wave_out").style.backgroundColor = c;
			document.getElementById("wave_in").style.borderColor = c;				
		}			
		</script>
		
		<div class="container">
		<h1><img border="0" src="/modules/calltoorder/admin/images/ico.png" style="float:left; margin-right:10px;" />Заказать звонок</h1>
	
		<form method="POST" action="/admin/modules/calltoorder/'.$m['id'].'/update">	
		
		<table class="admin_table_2">	
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Название модуля</td>
				<td><input type="text" class="input" name="title" size="20" value="'.$m['title'].'"></td>
			</tr>		
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Описание</td>
				<td>'.$m['description'].'</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Отображать:</td>
				<td>
					<select class="input" name="pub">
						<option value="1" '.$pub_1.'>Всегда</option>
						<option value="2" '.$pub_2.'>Только на компьютере</option>
						<option value="3" '.$pub_3.'>Только на телефоне</option>
						<option value="0" '.$pub_0.'>Никогда</option>
					</select>					
				</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Позиция вывода, блок</td>
				<td>
					<select class="input" size="1" name="block">
					'.$block_option.'
					</select>
					&nbsp;Определяет в каком месте (блоке) сайта вывести данный модуль
				</td>
			</tr>
			<tr>
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Вид отображения</td>
				<td>
					<select id="view" class="input" size="1" name="view" onchange="view_type();">
						<option value="0" '.$view_select_0.'>Обычная кнопка</option>
						<option value="1" '.$view_select_1.'>Кнопка поверх страницы</option>
					</select>
				</td>
			</tr>
			<tr id="tr_size">
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Размер кнопки</td>
				<td><input id="size" type="range" min="40" max="100" step="1" name="size" value="'.$m['p2'].'" onmousemove="size_value();"><span id="size_out" style="font-size:32px; margin-left:20px;"></span></td>
			</tr>
			<tr id="tr_right">
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Отступ справа</td>
				<td><input id="c_right" type="range" min="0" max="200" step="1" name="right" value="'.$m['p3'].'" onmousemove="right_value();"><span id="right_out" style="font-size:32px; margin-left:20px;"></span></td>
			</tr>
			<tr id="tr_bottom">
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Отступ снизу</td>
				<td><input id="c_bottom" type="range" min="0" max="200" step="1" name="bottom" value="'.$m['p4'].'" onmousemove="bottom_value();"><span id="bottom_out" style="font-size:32px; margin-left:20px;"></span></td>
			</tr>				
			<tr id="tr_color">
				<td width="20">&nbsp;</td>			
				<td width="200" height="25">Цвет кнопки</td>
				<td><input id="color" type="color" name="color"  value="'.$m['p5'].'" onchange="color_value();"></td>
			</tr>				
			<tr>
				<td width="20">&nbsp;</td>		
				<td width="200" height="25">Порядок следования</td>
				<td><input class="input" type="number" name="ordering" value="'.$m['ordering'].'" style="width:80px;"></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="1" '.$m['id'].' >
		<div style="margin-top:40px;">
		<input class="greenbutton" type="submit" value="Сохранить" name="bt_save">&nbsp;<input class="bluebutton" type="submit" value="Применить" name="bt_prim">&nbsp;<input class="graybutton" type="submit" value="Отменить" name="bt_none">
		</div>
		&nbsp;
		</form>	
		
		<div id="mod_calltoorder_circle">
			<div id="circle" class="mod_calltoorder_circle"></div>
			<div id="wave_out" class="mod_calltoorder_circle_wave_out" ></div>
			<div id="wave_in" class="mod_calltoorder_circle_wave_in"></div>
		</div>
		
		</div>
		<script type="text/javascript">view_type(); size_value(); right_value(); bottom_value(); color_value();</script>			
		';
	} // конец проверки 'enabled'
	else 
	{			
		echo '<div id="main-top">Модуль <b>"Заказать звонок"</b> не подключён</div>';
	}
} // конец функции


?>