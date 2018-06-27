DAN_ready(function(event)
{
	var margin_w = document.getElementById('margin_w');
	var margin_h = document.getElementById('margin_h');
	var bg_color = document.getElementById('bg_color');
	var editor1 = document.getElementById('editor1');
	var editor2 = document.getElementById('editor2');
	var mod_photo_s_hover = document.getElementsByClassName('mod_photo_s_hover')[0];	
	var mod_photo_s_content = document.getElementsByClassName('mod_photo_s_content')[0];

	bg_color.onchange = function(){mod_photo_s_hover.style.backgroundColor = bg_color.value;}
	margin_w.onchange = function(){mod_photo_s_content.style.marginLeft = margin_w.value + 'px';mod_photo_s_content.style.marginRight = margin_w.value + 'px';}
	margin_h.onchange = function(){mod_photo_s_content.style.marginTop = margin_h.value + 'px';mod_photo_s_content.style.marginBottom = margin_h.value + 'px';}
});