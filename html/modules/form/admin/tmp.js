DAN_ready(function(event)
{
	var editor_1 = document.getElementById('editor1');
	var editor_2 = document.getElementById('editor2');
	var title_pub = document.getElementById('titlepub');
	var title_pub_label = document.getElementById('titlepub_label');
	var text_pub = document.getElementById('text_pub');
	var text_label = document.getElementById('text_label');
	var filed_1 = document.getElementById('filed_1');
	var filed_1_pub = document.getElementById('filed_1_pub');
	var filed_1_label = document.getElementById('filed_1_label');
	var filed_2 = document.getElementById('filed_2');
	var filed_2_pub = document.getElementById('filed_2_pub');
	var filed_2_label = document.getElementById('filed_2_label');
	var filed_3 = document.getElementById('filed_3');
	var filed_3_pub = document.getElementById('filed_3_pub');
	var filed_3_label = document.getElementById('filed_3_label');
	var file = document.getElementById('file');
	var file_pub = document.getElementById('file_pub');
	var file_label = document.getElementById('file_label');
	var captcha = document.getElementById('captcha');
	var captcha_pub = document.getElementById('captcha_pub');
	var captcha_label = document.getElementById('captcha_label');



	f_inactive();



	title_pub_label.onclick = function(){
		if(title_pub.checked){editor_1.classList.add('inactive');} // Было включено
		else{editor_1.classList.remove('inactive');}
	};

	text_label.onclick = function(){
		if(text_pub.checked){editor_2.classList.add('inactive');} // Было включено
		else{editor_2.classList.remove('inactive');}
	};

	field_1_label.onclick = function(){
		if(field_1_pub.checked){field_1.classList.add('inactive');} // Было включено
		else{field_1.classList.remove('inactive');}
	};

	field_2_label.onclick = function(){
		if(field_2_pub.checked){field_2.classList.add('inactive');} // Было включено
		else{field_2.classList.remove('inactive');}
	};

	field_3_label.onclick = function(){
		if(field_3_pub.checked){field_3.classList.add('inactive');} // Было включено
		else{field_3.classList.remove('inactive');}
	};

	file_label.onclick = function(){
		if(file_pub.checked){file.classList.add('inactive');} // Было включено
		else{file.classList.remove('inactive');}
	};

	captcha_label.onclick = function(){
		if(captcha_pub.checked){captcha.classList.add('inactive');} // Было включено
		else{captcha.classList.remove('inactive');}
	};



	editor_1.onfocus = function() {
		editor_1.style.backgroundColor = '#fff';
	};

	editor_1.onblur = function() {
		editor_1.style.backgroundColor = '';
	};

	e_editor_2.on("focus", function() {
		editor_2.style.backgroundColor = '#fff';
	});

	e_editor_2.on("blur", function() {
		editor_2.style.backgroundColor = '';
	});



	function f_inactive()
	{
		if(!title_pub.checked){editor_1.classList.add('inactive');}
		if(!text_pub.checked){editor_2.classList.add('inactive');}
		if(!field_1_pub.checked){field_1.classList.add('inactive');}
		if(!field_2_pub.checked){field_2.classList.add('inactive');}
		if(!field_3_pub.checked){field_3.classList.add('inactive');}
		if(!file_pub.checked){file.classList.add('inactive');}
		if(!captcha_pub.checked){captcha.classList.add('inactive');}
	}
});