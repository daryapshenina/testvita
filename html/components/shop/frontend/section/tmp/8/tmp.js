DAN_ready(function(event)
{
	function section_resize()
	{
		var items = document.getElementsByClassName("section_item");

		if(items == null || items.length == 0)
			return;

		var frame = items[0].parentElement;
		var styles = document.getElementById('style_for_section_item');

		if(frame.offsetWidth < 1200)
		{
			if(styles != null)
				document.body.removeChild(styles);
		}
		else
		{
			if(styles == null)
			{
				var styles = document.createElement('style');
				styles.id = "style_for_section_item";
				styles.innerHTML = "\
					.section_item {\
						width:calc(50% - 10px);\
					}\
					\
					.section_item:nth-child(odd) {\
						margin-right:10px;\
					}\
					\
					.section_item:nth-child(even) {\
						margin-left:10px;\
					}\
				";
				document.body.appendChild(styles);
			}
		}
	}

	window.addEventListener("resize", section_resize);
	section_resize();
});
