function items_tmp_char()
{
	var sel = document.getElementById("char_sel");
	var item_id = document.getElementById("item_id").value;
	var si = sel.selectedIndex; // selectedIndex в select
	var char_value = sel.options[si].value;	
	var char_name = sel.name.substring(5,sel.name.length - 1); // char[Цвет] => Цвет

	item_id_next = items_group(item_id, char_name, char_value);
	
	// Если товар сменился - у нового товара устанавливаем selected
	if(item_id_next != item_id)
	{
		sel.options[si].selected = "";
		
		for(s in sel.options)
		{
			var sel = document.getElementById("char_sel"); // нужно переопределять, т.к DOM узел перестраивается.		

			if (char_value == sel.options[s].value)
			{
				sel.options[s].selected = true;
				break;
			}
		}
	}
}

