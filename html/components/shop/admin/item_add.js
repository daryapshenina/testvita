DAN_ready(function()
{
	document.getElementById('discount').onclick = e_discount;
	e_discount();	

	function e_discount()
	{
		var po = document.getElementById('price_old_display');
		
		if (document.getElementById('discount').checked == true)
		{
			po.style.display = 'inline';
		}
		else
		{
			po.style.display = 'none';		
		}
	}
});



