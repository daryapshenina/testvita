function color_value()
{
	//document.getElementById('editor2').style.backgroundColor = document.getElementById('color').value;
}


DAN_ready(function(event)
{
	var pad = 10; // padding	
	
	var size_type = document.getElementById('size');
	var flat_height = document.getElementById("flat_height");
	var flat_width = document.getElementById("flat_width");	
	var col_d = document.getElementById('width_d');
	var col_n = document.getElementById('width_n');
	var col_t = document.getElementById('width_t');
	var col_p = document.getElementById('width_p');
	var margin_w = document.getElementById('margin_w');
	var margin_h = document.getElementById('margin_h');
	
	var size_fix = document.getElementById('size_fix');
	var size_prc = document.getElementById('size_prc');		
	
	var container = document.getElementById('flat_container');
	var flat_white = document.getElementById('flat_white');

	size_type.onclick = f_size;
	flat_height.onchange = f_size;
	flat_width.onchange = f_size;
	col_d.onchange = f_size;
	col_n.onchange = f_size;
	col_t.onchange = f_size;
	col_p.onchange = f_size;
	margin_w.onchange = f_size;
	margin_h.onchange = f_size;
	
	f_size();

	function f_size()
	{
		var sel = document.getElementById('size');
		var si = sel.selectedIndex; // selectedIndex в select
		var size_value = sel.options[si].value;
	
		flat_white.style.marginLeft = parseInt(margin_w.value) + 'px';
		flat_white.style.marginRight = parseInt(margin_w.value) + 'px';
		flat_white.style.marginTop = parseInt(margin_h.value) + 'px';
		flat_white.style.marginBottom = parseInt(margin_h.value) + 'px';			

		if(size_value == 0) // Ширина в %
		{
			size_prc.style.display = 'table-row';
			size_fix.style.display = 'none';			
			
			var container_class = 'mod_flat_shadow_button_container col_d_' + col_d.value + ' col_n_' + col_n.value + ' col_t_' + col_t.value + ' col_p_' + col_p.value;
			container.className = container_class;
	
		//$w_out = 'width:calc(100% - 2*'.$margin[0].'px - 20px);';
		//$h_out = 'min-height:100px;';

			//flat_white.style.width = flat_width + 'px';
			
			flat_white.style.width = '';			
			flat_white.style.height = '';				
		}
		else // Фиксированная ширина
		{
			size_prc.style.display = 'none';
			size_fix.style.display = 'table-row';	
		
			container.className = 'mod_flat_shadow_button_container';
	
			flat_white.style.width = parseInt(flat_width.value) - 2*pad + 'px';
			flat_white.style.height = parseInt(flat_height.value) - 2*pad + 'px';	
		}
	}
	
	// Индикатор прозрачности
	var wid = document.getElementById("transparent");
	wid.onmousemove = function(){
		document.getElementById("transparent_out").innerHTML = wid.value;				
	}	
});