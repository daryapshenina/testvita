DAN_ready(function(event)
{
	var size_type = document.getElementById("size_type");
	var width_prc = document.getElementById("width_prc");
	var width_fix = document.getElementById("width_fix");
	var col_d = document.getElementById('width_d');
	var col_n = document.getElementById('width_n');
	var col_t = document.getElementById('width_t');
	var col_p = document.getElementById('width_p');
	var width_f = document.getElementById("width_f");
	var margin_w = document.getElementById('margin_w');
	var margin_h = document.getElementById('margin_h');
	var container = document.getElementById('editor_container');
	var bg_color_enable = document.getElementById('bg_color_enable');
	var bg_color = document.getElementById('bg_color');
	// var editable_area = document.getElementById('editor1');

	size_type.onchange = f_size;
	col_d.onchange = f_size;
	col_n.onchange = f_size;
	col_t.onchange = f_size;
	col_p.onchange = f_size;

	width_f.onchange = f_size;

	margin_w.onchange = f_size;
	margin_h.onchange = f_size;

	bg_color_enable.onclick = f_bg_color_enable;
	bg_color.onchange = f_bg_color;

	f_size();

	// При загрузке редактора - меняем фон
	e_editor.on("instanceReady", function() {
		f_bg_color_enable();
	});


	function f_size()
	{
		var si = size_type.selectedIndex; // selectedIndex в select
		var size_value = size_type.options[si].value;

		// editable_area.style.marginLeft = parseInt(margin_w.value) + 'px';
		// editable_area.style.marginRight = parseInt(margin_w.value) + 'px';
		// editable_area.style.marginTop = parseInt(margin_h.value) + 'px';
		// editable_area.style.marginBottom = parseInt(margin_h.value) + 'px';

		if(size_value == 0)
		{
			width_prc.style.display = 'table-row';
			width_fix.style.display = 'none';

			var container_class = 'mod_editor_container col_d_' + col_d.value + ' col_n_' + col_n.value + ' col_t_' + col_t.value + ' col_p_' + col_p.value;
			container.className = container_class;

			container.style.width = '';
		}
		else // Фиксированная ширина
		{
			width_prc.style.display = 'none';
			width_fix.style.display = 'table-row';

			container.className = 'mod_editor_container';

			if(parseInt(width_f.value) > 0)
				container.style.width = parseInt(width_f.value) + parseInt(margin_w.value) + 'px';
			else
				container.style.width = '';
		}
	}


	function f_bg_color_enable()
	{
		if(bg_color_enable.checked)
		{
			bg_color.style.display = 'block';
			f_bg_color();
		}
		else
		{
			bg_color.style.display = 'none';
			document.getElementsByClassName('cke_wysiwyg_div')[0].style.backgroundColor = '#ffffff';
		}
	}


	function f_bg_color()
	{
		if(bg_color_enable.checked)
		{
			if(document.getElementsByClassName('cke_wysiwyg_div')[0])
				document.getElementsByClassName('cke_wysiwyg_div')[0].style.backgroundColor = bg_color.value;
		}

	}
});