<?php
defined('AUTH') or die('Restricted access');

class classFilter 
{
	public function getSelect()
	{
		global $db;

		$query = $db->query("SELECT id, type, unit, name FROM com_shop_char_name ORDER BY ordering");

		$option = '';
		
		$s_arr = array();
		while($char = $query->fetch())
		{
			$option	.= '<option value='.$char['id'].' data-type="'.$char['type'].'" data-unit="'.$char['unit'].'">'.$char['name'].'</option>';
		}

		$filter_out = '<select onChange="char_insert();" id="fs" class="input"><option value=""></option>'.$option.'</select>';

		return $filter_out;
	}
	
	
	public function getFilter($section_id)
	{
		global $db;
		
		$stmt = $db->prepare("
			SELECT f.id, f.char_id, f.value_1, f.value_2, n.name, n.unit, n.type
			FROM com_shop_filter f
			JOIN com_shop_char_name n ON n.id = f.char_id
			WHERE f.section_id = :section_id 
			ORDER BY f.ordering		
		");
		
		$stmt->execute(array('section_id' => $section_id));

		$filter_out = '';
		
		$s_arr = array();
		while($filter = $stmt->fetch())
		{
			if($filter['type'] == 'number')
			{
				$type_out = 'число';
				$value_out = '<input onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" type="text" name="value_1[]" value="'.$filter['value_1'].'" class="input filer_input_number"> <input onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" type="text" name="value_2[]" value="'.$filter['value_2'].'" class="input filer_input_number">';
			}
			
			if($filter['type'] == 'string')
			{
				$type_out = 'строка';
				$value_out = '<input onFocus="drag_stop = 1;" onBlur="drag_stop = 0;" type="text" name="value_1[]" value="'.$filter['value_1'].'" class="input filer_input_string"> <input type="hidden" name="value_2[]" class="input filer_input_string">';
			}			
			$filter_out .= '<table class="drag_drop" draggable="true" data-name-id="'.$filter['char_id'].'" data-id="'.$filter['id'].'">';
			$filter_out	.= '<tr>';
			$filter_out	.= '<td class="filter_dnd"><div class="drag_move" title="Перетащите, что бы изменить порядок следования">&#9016;</div></td>';
			$filter_out	.= '<td class="filter_char">'.$filter['name'].'<input type="hidden" name="name_id[]" value="'.$filter['char_id'].'"></td>';
			$filter_out	.= '<td class="filter_unit"></td><td class="filter_type">'.$type_out.'</td>';
			$filter_out	.= '<td>'.$value_out.'</td>';
			$filter_out	.= '</tr>';
			$filter_out .= '</table>';			
		}

		return $filter_out;
	}	
}
?>