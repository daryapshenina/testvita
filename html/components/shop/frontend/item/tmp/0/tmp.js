function items_tmp_char()
{
	var sel = document.getElementById("char_sel");
	var item_id = document.getElementById("item_id").value;
	var si = sel.selectedIndex; // selectedIndex � select
	var char_value = sel.options[si].value;	
	var char_name = sel.name.substring(5,sel.name.length - 1); // char[����] => ����

	item_id_next = items_group(item_id, char_name, char_value);
	
	// ���� ����� �������� - � ������ ������ ������������� selected
	if(item_id_next != item_id)
	{
		sel.options[si].selected = "";
		
		for(s in sel.options)
		{
			var sel = document.getElementById("char_sel"); // ����� ��������������, �.� DOM ���� ���������������.		

			if (char_value == sel.options[s].value)
			{
				sel.options[s].selected = true;
				break;
			}
		}
	}
}

