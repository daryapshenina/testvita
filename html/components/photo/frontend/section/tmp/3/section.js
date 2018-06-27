function photo_like(_id){
	if(!getCookie("photo_item_like_" + _id)){
		document.cookie = "photo_item_like_" + _id + "=1;";
		document.getElementById("photo_item_like_" + _id).innerHTML++;
		
		photo_like_ajax(_id);
	}
	else {alert("Вы уже поставили лайк этой фотографии");}
}


// возвращает cookie с именем name, если есть, если нет, то null
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : null;
}


function photo_like_ajax(_id)
{	
	var req = getXmlHttp();
	req.onreadystatechange = function() 
	{
		if (req.readyState == 4) 
		{
			if(req.status == 200) 
			{
				console.log('Ok!');
			}
		}
	
	}
	req.open('GET', 'http://' + document.domain + '/photo/section/like/'+_id, true);
	req.send(null);
}
