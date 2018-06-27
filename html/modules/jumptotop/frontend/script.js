DAN_ready(function(event)
{
	var MAX_OPACITY = 0.7;
	var MIN_OPACITY = 0;
	var SPEED_TO_TOP = 70;

	function jumpToTop()
	{
		var heightDocument = document.documentElement.clientHeight;
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		var obj = document.getElementById("mod_jumptotop_main");

		if(obj != null)
		{
			if(scrollTop > (heightDocument / 2))
			{
				var opacityNew = scrollTop / ((heightDocument * 2) / 100);
				opacityNew /= 100;

				if(opacityNew > MAX_OPACITY)
				{
					opacityNew = MAX_OPACITY;
				}

				obj.style.opacity = opacityNew;
				obj.style.display = "block";
			}
			else
			{
				obj.style.opacity = MIN_OPACITY;
				obj.style.display = "none";
			}
		}
	}

	function jumpToTopAnimation()
	{
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		if(scrollTop > 0)
		{
			scrollTop -= SPEED_TO_TOP;
			window.scrollTo(0, scrollTop);
			setTimeout(jumpToTopAnimation, 1);
		}
	}

	function jumpToTopClick()
	{
		jumpToTopAnimation();
	}

	window.addEventListener("scroll", jumpToTop);
	jumpToTop();

	var mainObj = document.getElementById("mod_jumptotop_main");
	if(mainObj != null)
		mainObj.addEventListener("click", jumpToTopClick);
});
