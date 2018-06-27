<?php
// DAN 2012
// выводит заголовок, описание и фильтры раздела
defined('AUTH') or die('Restricted access');

echo
'
<div class="title">
	<div class="title-1"></div>
	<div class="title-2"><h1>'.$author.'</h1></div>
	<div class="title-3"></div>			
</div>
<br/>
<div>'.$author_description.'</div>
<div>&nbsp;</div>
';


// ------- Голосование на AJAX -------
echo '	
<script type="text/javascript">	

function getXmlHttp(){
  var xmlhttp;
  try {
	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
	try {
	  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (E) {
	  xmlhttp = false;
	}
  }
  if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
	xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}

function vote(id,vote)
{
	vts = "votestatus_" + id;	
	var req = getXmlHttp()  
	req.onreadystatechange = function() 
	{
		if (req.readyState == 4) 
		{
			if(req.status == 200) 
			{
				document.getElementById(vts).innerHTML = req.responseText;
			}
		}
	
	}
	
	req.open(\'GET\', \'http://'.$site.'/components/quote/frontend/quote_vote.php?id=\' + id + \'&vote=\' + vote, true);
	req.send(null);
	document.getElementById(vts).innerHTML = "<div align=\"center\"><img class=\"loading_img\" src=\"http://'.$site.'/components/quote/frontend/tmp/images/loading.gif\" /></div>";
}

</script>
';

?>