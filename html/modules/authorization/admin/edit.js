function f_url()
{
	var sel = document.getElementById('mod_authorization_url_select');
	var si = sel.selectedIndex; // selectedIndex â select
	var value = sel.options[si].value;
	var tr = document.getElementById('mod_mod_authorization_url_tr');
	
	if(value == "0"){
		tr.style.display = "table-row";
	}
	else {
		tr.style.display = "none";	
	}
}