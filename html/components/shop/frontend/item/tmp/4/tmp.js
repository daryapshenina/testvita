﻿function items_tmp_char()
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

function items_tmp_char_color(_char_name, _color)
{
	console.log(_color);

	var item_id = document.getElementById("item_id").value;

	item_id_next = items_group(item_id, _char_name, _color);

	document.getElementsByClassName("char_color_item_selected")[0].classList.remove("char_color_item_selected"); // Удаляем выделение на старом элементе
	document.getElementById("char_color_" + _color).classList.add("char_color_item_selected");
	document.getElementById("char_color_input").value = _color; // устанавливаем цвет у скрытого поля для корзины
}


DAN_ready(function(event)
{
	function item_resize()
	{
		var item_main = document.getElementById("item_main");
		if(item_main.offsetWidth < 750)
		{
			document.getElementById("item_photo_container").style.display = "block";
			document.getElementById("item_photo_container").style.width = "100%";
			document.getElementById("item_introtext_container").style.display = "block";
		}
		else
		{
			document.getElementById("item_photo_container").style.display = "table-cell";
			document.getElementById("item_photo_container").style.width = "50%";
			document.getElementById("item_introtext_container").style.display = "table-cell";
		}
	}

	window.addEventListener("resize", item_resize);
	item_resize();


	function item_quantity(n)
	{
		var q = document.getElementById("input_quantity");
		q.value = parseInt(q.value) + n;
		if(q.value < 1){q.value = 1;}
	}

	var minus = document.getElementById("quantity_minus");
	var plus = document.getElementById("quantity_plus");

	minus.onclick = function(){item_quantity(-1);}
	plus.onclick = function(){item_quantity(1);}
});
