DAN_ready(function()
{
	if(document.getElementById('mod_search_submit'))
	{
	// ������ ������
	document.getElementById('mod_search_submit').onclick = fsubmit;
	}
	
	function fsubmit()
	{
		setTimeout("document.mod_form_search.submit()", 10);
	}
});