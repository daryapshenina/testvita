<?php
defined('AUTH') or die('Restricted access');

// Наследуем класс classChars (он общий для frontend и admin)
class classCharsAdmin extends classChars
{
	// Возвращает шаблон вывода характеристик	
	protected function CharsTemplate($item_id)
	{
		$out = '';

		foreach($this->getCharsArray($item_id) as $char) // цикл по строкам
		{
			if($char['type'] == 'number')
			{
				$type_out = 'число';
				$value_out = '<input draggable="false" onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" class="input char_input_number" type="text" name="char_value[]" value="'.$char['value'].'">';
			}
			if($char['type'] == 'string')
			{
				$type_out = 'строка';
				$value_out = '<input draggable="false" onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" class="input char_input_string" type="text" name="char_value[]" value="'.$char['value'].'">';				
			}					
			
			$out .= '<table class="char_tab" draggable="true" data-id="'.$char['id'].'">';
			$out .= '<tr>';
			$out .= '<td class="char_dnd"><div class="char_move" title="Перетащите, что бы изменить порядок следования">&#9016;</div></td>';
			$out .= '<td class="char_name">'.$char['name'].'<input type="hidden" name="char_id[]" value="'.$char['id'].'"><input type="hidden" name="char_name_id[]" value="'.$char['name_id'].'"></td>';
			$out .= '<td class="char_unit">'.$char['unit'].'</td>';
			$out .= '<td class="char_type">'.$type_out.'</td>';
			$out .= '<td class="char_value"><input draggable="false" onfocus="drag_stop = 1;" onblur="drag_stop = 0;" class="input char_input_'.$char['type'].'" type="text" name="char_value[]" value="'.$char['value'].'"></td>';
			$out .= '<td>&nbsp;</td>';
			$out .= '</tr>';
			$out .= '</table>';		
		}

		return $out;
	}
}


?>




		
			

