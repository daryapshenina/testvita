DAN_ready(function(event)
{
	scroll = function()
	{

		var scrolled = window.pageYOffset || document.documentElement.scrollTop;

		// === top_menu ==========
		var top = document.getElementById('top');
		var header = document.getElementById('header');

		if (scrolled > top.clientHeight && top.clientWidth > 740)
		{
			if (header.className != 'header_fixed'){header.className = 'header_fixed';}
		}
		else
		{
			if (header.className == 'header_fixed'){header.className = '';}
		}

		// === функция анимации ==========
		animation = function(id, anim)
		{
			var anim_id = document.getElementById(id);

			if (scrolled > (anim_id.offsetTop - window.innerHeight))
			{
				if (anim_id.className != anim){anim_id.className = anim;}
			}
			else
			{
				if (anim_id.className == anim){anim_id.className = '';}
			}
		}

		animation ('module_four_1', 'animation_show_to_top_1');
		animation ('module_four_2', 'animation_show_to_top_2');
		animation ('module_four_3', 'animation_show_to_top_3');
		animation ('module_four_4', 'animation_show_to_top_4');

		animation ('mt_1', 'animation_show_to_right');
		animation ('mt_2', 'animation_show_to_left');
	}

	window.onscroll = scroll;
	scroll();

});
