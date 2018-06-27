<?php
// DAN 2013
// Настройки интернет магазина

defined('AUTH') or die('Restricted access');

$item_id = intval($admin_d4); 

function a_com()
{ 
	global $site, $item_id, $item_section_id ; 
	
	echo '
		<div id="main-top"><img border="0" src="http://'.$site.'/administrator/tmp/images/tools.png" width="25" height="25"  style="vertical-align: middle" />&nbsp;&nbsp;Настройки интернет-магазина:</div>
		<div>&nbsp;</div>
		
		<table class="w100_bs1 menuheader">
			<tr>
				<td class="cell-v"></td>
				<td class="cell-title-modules" >Параметр</td>
				<td class="cell-desc-modules" >Значение</td>			
			</tr>
		</table>		
	';	
	
		// вывод настроек	
		$num = mysql_query("SELECT * FROM `com_shop_settings`") or die ("Невозможно сделать выборку из таблицы - 1");
			
		while($m = mysql_fetch_array($num)):
			$setting_id = $m['id'];
			$setting_name = $m['name'];
			$setting_parameter = $m['parametr'];
			
			// почта
			if ($setting_name == "email")
			{
				$shop_email = '<input type="email" name="shopemail" size="50" value="'.$setting_parameter.'" required >';
			} 	
			
			// размер по "х" малого изображения 
			if ($setting_name == "x_small")
			{
				$x_small= '<input type="number" min="50" max="500" name="xsmall" size="3" value="'.$setting_parameter.'" required >';
			} 	
			
			// размер по "y" малого изображения 
			if ($setting_name == "y_small")
			{
				$y_small= '<input type="number" min="50" max="500" name="ysmall" size="3" value="'.$setting_parameter.'" required >';
			} 

			// размер по "х" большого изображения 
			if ($setting_name == "x_big")
			{
				$x_big= '<input type="number" min="400" max="1000" name="xbig" size="3" value="'.$setting_parameter.'" required >';
			} 	
			
			// размер по "y" малого изображения 
			if ($setting_name == "y_big")
			{
				$y_big= '<input type="number" min="400" max="1000" name="ybig" size="3" value="'.$setting_parameter.'" required >';
			} 			
			
			// метод ресайза
			if ($setting_name == "small_resize_method")
			{
				$small_resize_method_checked = $setting_parameter;
				if ($small_resize_method_checked == "1"){$srm_checked_1 = "checked";} else {$srm_checked_1 = "";}
				if ($small_resize_method_checked == "2"){$srm_checked_2 = "checked";} else {$srm_checked_2 = "";}
				if ($small_resize_method_checked == "3"){$srm_checked_3 = "checked";} else {$srm_checked_3 = "";}
			} 	
			
			// количество товаров на странице 
			if ($setting_name == "quantity")
			{
				$quantity = '<input type="number" min="10" max="1000"  name="quantity" size="3" value="'.$setting_parameter.'" required >';
			} 	
			
			// отображение товаров на странице 
			if ($setting_name == "mapping")
			{
				$mapping_method_checked = $setting_parameter;
				if ($mapping_method_checked == "1"){$mapping_checked_1 = "checked";} else {$mapping_checked_1 = "";}
				if ($mapping_method_checked == "2"){$mapping_checked_2 = "checked";} else {$mapping_checked_2 = "";}
				if ($mapping_method_checked == "3"){$mapping_checked_3 = "checked";} else {$mapping_checked_3 = "";}	
				if ($mapping_method_checked == "4"){$mapping_checked_4 = "checked";} else {$mapping_checked_4 = "";}	
				if ($mapping_method_checked == "5"){$mapping_checked_5 = "checked";} else {$mapping_checked_5 = "";}
			} 
						
			// сортировка товара на странице
			if ($setting_name == "sorting_items")
			{
				$sorting_cheked = $setting_parameter;
				if ($sorting_cheked == "0"){$sorting_cheked_0 = "selected";}
				elseif ($sorting_cheked == "1"){$sorting_cheked_1 = "selected";}
				elseif ($sorting_cheked == "2"){$sorting_cheked_2 = "selected";}
				elseif ($sorting_cheked == "3"){$sorting_cheked_3 = "selected";}
				elseif ($sorting_cheked == "4"){$sorting_cheked_4 = "selected";}
				elseif ($sorting_cheked == "5"){$sorting_cheked_5 = "selected";}
				else {$sorting_cheked_0 = "selected";}
			} 	
			
			if ($setting_name == "output_un_section")
			{
				if ($setting_parameter == 1)
				{
					$output_un_section_cheked = 'checked="checked"';
				}
				else
				{
					$output_un_section_cheked = '';
				}
			}
			
			if($setting_name == "view_item_card")
			{
				if($setting_parameter == 0){$view_item_card_1 = 'selected="selected"';}
				if($setting_parameter == 1){$view_item_card_2 = 'selected="selected"';}
			}

			if($setting_name == "question")
			{
				if($setting_parameter == 1)
				{
					$question_select = 'checked="checked"';
				}
				else
				{
					$question_select = '';
				}
			}
			
			if($setting_name == "item_quantity")
			{
				if($setting_parameter == 1)
				{
					$item_quantity_checked_1 = 'checked="checked"';
					$item_quantity_checked_0 = '';
					$item_quantity_checked_2 = '';					
				}
				elseif($setting_parameter == 2)
				{
					$item_quantity_checked_2 = 'checked="checked"';
					$item_quantity_checked_0 = '';
					$item_quantity_checked_1 = '';					
				}				
				else
				{
					$item_quantity_checked_0 = 'checked="checked"';
					$item_quantity_checked_1 = '';
					$item_quantity_checked_2 = '';
				}
			}

			if($setting_name == "section_description")
			{
				if($setting_parameter == 1)
				{
					$section_description_checked_1 = 'checked="checked"';
					$section_description_checked_0 = '';					
				}				
				else
				{
					$section_description_checked_0 = 'checked="checked"';
					$section_description_checked_1 = '';
				}
			}			
			
			if($setting_name == "basket_type")
			{
				if($setting_parameter == 0)
				{
					$basket_type_0 = 'checked="checked"';
					$basket_type_1 = '';
				}
				elseif($setting_parameter == 1)
				{
					$basket_type_1 = 'checked="checked"';
					$basket_type_0 = '';
				}				
			}			
			
			
		endwhile; 
		
	// вывод параметров	
	echo'	
		<form method="POST" action="http://'.$site.'/admin/com/shop/settingsupdate">			
		<table class="w100_bs1">
			<tr>
				<td class="cell-v ">&nbsp;</td>
				<td class="cell-title-modules">&nbsp;</td>
				<td class="cell-desc-modules">&nbsp;</td>
			</tr>
			<tr>
				<td class="cell-v shop-tab-title">&nbsp;</td>
				<td class="cell-title-modules shop-tab-title"><span class="lineheight20"><b>Общие настройки:</b></span></td>
				<td class="cell-desc-modules shop-tab-title">&nbsp;</td>
			</tr>			
			<tr>
				<td class="cell-v bg_gray_1">1</td>
				<td class="cell-title-modules bg_gray_1"><b>Email получения заказов</b></td>
				<td class="cell-desc-modules">'.$shop_email.'</td>
			</tr>		
			<tr>
				<td class="cell-v bg_gray_1">2</td>
				<td class="cell-title-modules bg_gray_1"><b>Размер малого изображения</b></span></td>
				<td class="cell-desc-modules">по ширине: '.$x_small.' px. &nbsp;&nbsp; по высоте: '.$y_small.' px.</td>
			</tr>
			<tr>
				<td class="cell-v bg_gray_1">3</td>
				<td class="cell-title-modules bg_gray_1"><b>Размер большого изображения</b></span></td>
				<td class="cell-desc-modules">по ширине: '.$x_big.' px. &nbsp;&nbsp; по высоте: '.$y_big.' px.</td>
			</tr>			
			<tr>
				<td class="cell-v bg_gray_1">4</td>
				<td class="cell-title-modules bg_gray_1"><b>Метод создания <br/>малого изображения:</b></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="1" '.$srm_checked_1.' name="smallresizemethod">умный ресайз <i>(вставка по большей стороне)</i> <br/>
						<input type="radio" value="2" '.$srm_checked_2.' name="smallresizemethod">подрезка <i>(подрезка большей стороны)</i><br/>
						<input type="radio" value="3" '.$srm_checked_3.' name="smallresizemethod">скукожить <i>(смять, пропорции игнорируются)</i><br/>
					</span>
				</td>
			</tr>
			<tr>
				<td class="cell-v shop-tab-title">&nbsp;</td>
				<td class="cell-title-modules shop-tab-title"><b>Раздел:<b></td>
				<td class="cell-desc-modules shop-tab-title">&nbsp;</td>
			</tr>			
			<tr>
				<td class="cell-v bg_gray_1">5</td>
				<td class="cell-title-modules bg_gray_1"><b>Количество товаров на странице</b></td>
				<td class="cell-desc-modules">'.$quantity.'</td>
			</tr>
			<tr>
				<td class="cell-v bg_gray_1">6</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Вид отображения <br/>товаров в категории:</b></span></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="1" '.$mapping_checked_1.' name="mappingmethod">Блоком в одну строку <br/>
						<input type="radio" value="2" '.$mapping_checked_2.' name="mappingmethod">Плиткой <br/>
						<input type="radio" value="5" '.$mapping_checked_5.' name="mappingmethod">Плиткой + всплывающие картинки<br/>
						<input type="radio" value="4" '.$mapping_checked_4.' name="mappingmethod">Плиткой (старый стиль)<br/>
						<input type="radio" value="3" '.$mapping_checked_3.' name="mappingmethod">Карточками <br/>							
					</span>
				</td>
			</tr>
			<tr>
				<td class="cell-v bg_gray_1">7</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Сортировка товара:</b></span></td>
				<td class="cell-desc-modules">
					<select name="sorting_items">
						<option value="0" '.$sorting_cheked_0.'>Ручная (Настраивается при добавлении или редактировании товара)</option>
						<option value="1" '.$sorting_cheked_1.'>По цене (по возрастанию)</option>
						<option value="2" '.$sorting_cheked_2.'>По цене (по убыванию)</option>
						<option value="3" '.$sorting_cheked_3.'>По алфавиту (по возрастанию)</option>
						<option value="4" '.$sorting_cheked_4.'>По алфавиту (по убыванию)</option>
						<option value="5" '.$sorting_cheked_5.'>По дате (Новые сверху включая недавно редактируемые)</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="cell-v bg_gray_1">8</td>
				<td class="cell-title-modules bg_gray_1"><b>Вывод описания раздела</b></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="0" '.$section_description_checked_0.' name="section_description">Сверху<br/>
						<input type="radio" value="1" '.$section_description_checked_1.' name="section_description">Снизу
					</span>
				</td>
			</tr>				
			<tr>
				<td class="cell-v bg_gray_1">9</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Вывод товаров из подразделов</b></span></td>
				<td class="cell-desc-modules">
					<input type="checkbox" name="output_un_section" value="1" '.$output_un_section_cheked.'>
				</td>
			</tr>				
			<tr>
				<td class="cell-v shop-tab-title">&nbsp;</td>
				<td class="cell-title-modules shop-tab-title"><b>Товар</b></td>
				<td class="cell-desc-modules shop-tab-title">&nbsp;</td>
			</tr>
			<tr>
				<td class="cell-v bg_gray_1">10</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Карточка товаров</b></span></td>
				<td class="cell-desc-modules">
					<select name="view_item_card">
						<option value="0" '.$view_item_card_1.'>Обычный</option>
						<option value="1" '.$view_item_card_2.'>Расширенный</option>
					</select>
				</td>
			</tr>				
			<tr>
				<td class="cell-v bg_gray_1">11</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Задать вопрос по товару</b></span></td>
				<td class="cell-desc-modules">
					<input type="checkbox" name="button_question" value="1" '.$question_select.'> Отображает ссылку на форму в которой посетитель может задать вопрос
				</td>
			</tr>
			<tr>
				<td class="cell-v bg_gray_1">12</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Учитывать количество товаров</b></span></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="0" '.$item_quantity_checked_0.' name="item_quantity">Не учитывать количество.<br/>
						<input type="radio" value="1" '.$item_quantity_checked_1.' name="item_quantity">Учитывать. В административной части в карточке товара появляется поле <b>&quot;Количество&quot;</b>. Товар с нулевым количеством получает статус <b>&quot;Под заказ&quot;</b>.<br/>						
						<input type="radio" value="2" '.$item_quantity_checked_2.' name="item_quantity">Учитывать. В административной части в карточке товара появляется поле <b>&quot;Количество&quot;</b>. При загрузке товаров из 1С - скрыть товар с нулевым количеством.<br/>			
					</span>
				</td>
			</tr>
			<tr>
				<td class="cell-v shop-tab-title">&nbsp;</td>
				<td class="cell-title-modules shop-tab-title"><b>Корзина:<b></td>
				<td class="cell-desc-modules shop-tab-title">&nbsp;</td>
			</tr>			
			<tr>
				<td class="cell-v bg_gray_1">13</td>
				<td class="cell-title-modules bg_gray_1"><span class="lineheight20"><b>Оформление покупки</b></span></td>
				<td class="cell-desc-modules">
					<span class="lineheight20">
						<input type="radio" value="0" '.$basket_type_0.' name="basket_type">Обычное (перейти в корзину / продолжить покупки)<br/>
						<input type="radio" value="1" '.$basket_type_1.' name="basket_type">Летающее (товар "улетает" в корзину)
					</span>
				</td>
			</tr>			
			<tr>
				<td class="cell-v">&nbsp;</td>
				<td class="cell-title-modules">&nbsp;</td>
				<td class="cell-desc-modules">&nbsp;</td>
			</tr>
		</table>
		<br/>
		&nbsp;&nbsp;<input class="greenbutton" type="submit" value="Сохранить" name="bt">&nbsp;<input class="redbutton" type="submit" value="Отменить" name="none">
		<br/>
		&nbsp;			
		</form>		
	';	


}

?>
