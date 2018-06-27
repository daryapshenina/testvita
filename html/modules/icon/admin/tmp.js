DAN_ready(function(event)
{
	var icon_style = document.getElementById("icon_style");	
	var size_type = document.getElementById('size_type');
	var width_prc_tr = document.getElementById('width_prc_tr');	
	var width_fix_tr = document.getElementById('width_fix_tr');
	var height_fix_tr = document.getElementById('height_fix_tr');
	var col_d = document.getElementById('width_d');
	var col_n = document.getElementById('width_n');
	var col_t = document.getElementById('width_t');
	var col_p = document.getElementById('width_p');
	var width_fix = document.getElementById("width_fix");
	var height_fix = document.getElementById("height_fix");
	var margin_w = document.getElementById('margin_w');
	var margin_h = document.getElementById('margin_h');
	var padding_w = document.getElementById('padding_w');
	var padding_h = document.getElementById('padding_h');
	var color = document.getElementById('color');
	var icon_size = document.getElementById('icon_size');	
	var icon_container = document.getElementById('icon_container');
	var icon_main = document.getElementById('icon_main');
	var icon_frame = document.getElementById('icon_frame');	
	var icon_solid = document.getElementById('icon_solid');
	var icon_type = document.getElementById('icon_type');	
	var icon_type_input = document.getElementById('icon_type_input');	
	
	icon_style.onchange = f_icon_style;
	size_type.onchange = f_size_type;
	col_d.onchange = f_size_type;
	col_n.onchange = f_size_type;
	col_t.onchange = f_size_type;
	col_p.onchange = f_size_type;
	width_fix.onchange = f_size_type;
	height_fix.onchange = f_size_type;
	margin_w.onchange = f_size_type;
	margin_h.onchange = f_size_type;
	padding_w.onchange = f_size_type;
	padding_h.onchange = f_size_type;
	color.onchange = f_color;
	icon_size.onchange = f_icon_size;
	
	f_size_type();


	function f_icon_style()
	{
		icon_frame.className = 'mod_icon_' + icon_style.value + '_frame';
	}


	function f_size_type()
	{	
		icon_main.style.margin = parseInt(margin_h.value) + 'px ' + parseInt(margin_w.value) + 'px';		
		icon_main.style.padding = parseInt(padding_h.value) + 'px ' + parseInt(padding_w.value) + 'px';	
		
		if(size_type.value == 1) // Фиксированное значение
		{
			width_prc_tr.style.display = 'none';
			width_fix_tr.style.display = 'table-row';
			height_fix_tr.style.display = 'table-row';

			icon_container.className = '';

			if(parseInt(width_fix.value) + parseInt(margin_w.value) > 0) 
			{
				icon_container.style.width = parseInt(width_fix.value) + 2*parseInt(margin_w.value) + 'px';
				icon_main.style.width = parseInt(width_fix.value) - 2*parseInt(padding_w.value) + 'px';			
			}
			else 
			{
				icon_container.style.width = '';
				icon_main.style.width = '';
			} 
			
			if(parseInt(height_fix.value) + parseInt(margin_h.value) > 0)
			{
				icon_container.style.height = parseInt(height_fix.value) + 2*parseInt(margin_h.value) + 'px';
				icon_main.style.height = parseInt(height_fix.value) - 2*parseInt(padding_h.value) + 'px';			
			}
			else
			{
				icon_container.style.height = '';
				icon_main.style.height = '';					
			}
			
		}
		else
		{
			width_prc_tr.style.display = 'table-row';
			width_fix_tr.style.display = 'none';
			height_fix_tr.style.display = 'none';

			icon_container.className = 'col_d_' + col_d.value + ' col_n_' + col_n.value + ' col_t_' + col_t.value + ' col_p_' + col_p.value;

			icon_container.style.width = '';
			icon_container.style.height = '';
			icon_main.style.width = '';
			icon_main.style.height = '';
		}
	}

	
	function f_color()
	{
		icon_type.style.color = color.value;
		icon_solid.style.backgroundColor = color.value;
		icon_frame.style.boxShadow =  '0 0 0 3px ' + color.value + ' inset';
	}
	
	
	function f_icon_size()
	{
		icon_frame.style.width = icon_frame.style.height = icon_size.value + 'px';
		icon_type.style.lineHeight = icon_size.value + 'px';
		icon_type.style.fontSize = parseInt(icon_size.value/2.5) + 'px';
		
		console.log(parseInt(icon_size.value/2.5));
	}
});	

function icon_select(_icon)
{
	icon_type.className = 'fa fa-' + _icon;
	icon_type_input.value = _icon;

	var accordion_icon_list = document.getElementById('accordion_icon_list');
	accordion_icon_list.classList.remove('expand');
}

