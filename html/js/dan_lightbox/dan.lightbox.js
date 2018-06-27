DAN_ready(dan_lightbox);function dan_lightbox()
{if(!document.getElementsByClassName){document.getElementsByClassName=function(class_name){var elements=document.body.getElementsByTagName("*"),length=elements.length,out=[],i;for(i=0;i<length;i+=1)
{if(elements[i].className.indexOf(class_name)!==-1){out.push(elements[i]);}}
return out;};}
var dan_show=document.getElementsByClassName('show');var dan_show_length=dan_show.length;var play_start=0;var dan_lbox=new Array();var dan_lightbox=new Array();images();for(var i=0;i<dan_show_length;i++)
{var img_count=0;dan_lbox[i].onload=function(){for(var i=0;i<dan_show_length;i++)
{dan_show[i].onclick=function()
{if(document.getElementById('dan_lightbox')==null)
{black();white();img_big=images_this(this,true);navigation();var img_start='';var img_end=images_this(this,false);resize(img_start,img_end);}
return false;}}}}
function waiting(w)
{if(w==1)
{if(!document.getElementById('dan_lightbox_waiting'))
{dan_lightbox_waiting=document.createElement('img');dan_lightbox_waiting.id='dan_lightbox_waiting';dan_lightbox_waiting.style.zIndex=1010;var dan_body_child_0=document.body.children[0];document.body.insertBefore(dan_lightbox_waiting,dan_body_child_0);}}
else
{if(typeof(dan_lightbox_waiting)!=="undefined"){document.body.removeChild(dan_lightbox_waiting);}}}
function black()
{var dan_lightbox_black=document.createElement('div');dan_lightbox_black.id='dan_lightbox_black';dan_lightbox_black.title='Закрыть окно';dan_lightbox_black.style.backgroundColor='#000000';dan_lightbox_black.style.opacity=0;dan_lightbox_black.style.filter='alpha(opacity = 0)';var dan_body_child_0=document.body.children[0];document.body.insertBefore(dan_lightbox_black,dan_body_child_0);var op=0;timer_black=setInterval(function(){if(op<=50)
{dan_lightbox_opacity=op/100;document.getElementById('dan_lightbox_black').style.opacity=dan_lightbox_opacity;op=op+5;}
else
{document.getElementById('dan_lightbox_black').style.opacity=0.5;clearTimeout(timer_black);}},20);dan_lightbox_black.onclick=close;}
function white(width,height)
{var dan_lightbox_white=document.createElement('div');dan_lightbox_white.id='dan_lightbox_white';var dan_body_child_0=document.body.children[0];document.body.insertBefore(dan_lightbox_white,dan_body_child_0);}
function images(img_this)
{for(var i=0;i<dan_show_length;i++)
{dan_lbox[i]=document.createElement('img');if(dan_show[i].tagName=='A'){dan_lbox[i].src=dan_show[i];}
if(dan_show[i].tagName=='IMG'){dan_lbox[i].src=dan_show[i].src;}}}
function images_this(img_this,add_to_white)
{if(img_this.tagName=='A'){img_this_src=img_this;}
if(img_this.tagName=='IMG'){img_this_src=img_this.src;}
for(var i=0;i<dan_show_length;i++)
{if(add_to_white)
{var img_out=document.createElement('img');img_out.src=dan_lbox[i].src;img_out=dan_img_size(img_out);img_out.className='dan_lightbox_images';img_out.style.display='none';img_out.style.opacity=0;img_out.style.filter='alpha(opacity = 0)';dan_lightbox[i]=dan_lightbox_white.appendChild(img_out);}
if(dan_lightbox[i].src==img_this_src){img_return=dan_lightbox[i];}}
return img_return;}
function dan_img_size(img_this)
{var displayHeight=document.documentElement.clientHeight;var displayWidth=document.documentElement.clientWidth;var dan_lightbox_end_h=img_this.naturalHeight;var dan_lightbox_end_w=img_this.naturalWidth;var k_H=dan_lightbox_end_h/displayHeight;var k_W=dan_lightbox_end_w/displayWidth;if(k_W>k_H)
{if(k_W>0.8)
{dan_lightbox_end_h=parseInt(0.8*dan_lightbox_end_h*(displayWidth/dan_lightbox_end_w));dan_lightbox_end_w=parseInt(0.8*displayWidth);}}
else
{if(k_H>0.8)
{dan_lightbox_end_w=parseInt(0.8*dan_lightbox_end_w*(displayHeight/dan_lightbox_end_h));dan_lightbox_end_h=parseInt(0.8*displayHeight);}}
img_this.style.height=dan_lightbox_end_h+'px';img_this.style.width=dan_lightbox_end_w+'px';return img_this;}
function navigation()
{var nav_prev=document.createElement('div');nav_prev.id='dan_lightbox_nav_prev';dan_lightbox_white.appendChild(nav_prev);nav_prev.onclick=prev;var nav_next=document.createElement('div');nav_next.id='dan_lightbox_nav_next';dan_lightbox_white.appendChild(nav_next);nav_next.onclick=next;var nav_play=document.createElement('div');nav_play.id='dan_lightbox_nav_play';dan_lightbox_white.appendChild(nav_play);nav_play.onclick=play;if(dan_show_length>1)
{document.getElementById('dan_lightbox_nav_next').style.display='block';document.getElementById('dan_lightbox_nav_prev').style.display='block';document.getElementById('dan_lightbox_nav_play').style.display='block';}
var dan_lightbox_close=document.createElement('div');dan_lightbox_close.id='dan_lightbox_close';dan_lightbox_close.title='Закрыть окно';dan_lightbox_white.appendChild(dan_lightbox_close);dan_lightbox_close.onclick=close;document.getElementById('dan_lightbox_close').style.opacity=1;}
function resize(img_start,img_end)
{stop_animation(img_start,img_end);if(!img_end.complete){waiting(1);img_end.onload=function(){waiting(0);dan_img_size(img_end);resize_height(img_start,img_end);}}
else
{if(img_end.style.width=='0px'){dan_img_size(img_end);}
resize_height(img_start,img_end);}
function resize_height(img_start,img_end)
{if(img_start==''||img_start=='undefined')
{var height_start=100;}
else
{img_start.style.display='none';img_start.style.opacity=0;height_start=parseInt(img_start.style.height);if(height_start==0)
{dan_img_size(img_start);height_start=parseInt(dan_img_size(img_start).style.height);}}
img_end.style.display='inline';var height_end=parseInt(img_end.style.height);var height_step=parseInt((height_end-height_start)/5);if(height_start!=height_end)
{var i=0;timer_resize_height=setInterval(function(){i++;height_start=height_start+height_step;document.getElementById('dan_lightbox_white').style.height=height_start+'px';document.getElementById('dan_lightbox_white').style.marginTop='-'+parseInt(height_start/2+20)+'px';if(i>4)
{document.getElementById('dan_lightbox_white').style.height=height_end+'px';document.getElementById('dan_lightbox_white').style.marginTop='-'+parseInt(height_end/2+20)+'px';stop_animation();resize_width(img_end);}},20);}
else{resize_width(img_end);}}
function resize_width(img_end)
{stop_animation();if(img_start==''||img_start=='undefined')
{var width_start=100;}
else
{width_start=parseInt(img_start.style.width);if(width_start==0)
{width_start=parseInt(dan_img_size(img_start).style.width);}}
var width_end=parseInt(dan_img_size(img_end).style.width);if(width_start==0){width_start=100;}
if(width_end==0){width_end=100;}
var width_step=parseInt((width_end-width_start)/5);if(width_start!=width_end)
{var j=0;timer_resize_width=setInterval(function(){j++;width_start=width_start+width_step;document.getElementById('dan_lightbox_white').style.width=width_start+'px';document.getElementById('dan_lightbox_white').style.marginLeft='-'+parseInt(width_start/2+20)+'px';if(j>4)
{document.getElementById('dan_lightbox_white').style.width=width_end+'px';document.getElementById('dan_lightbox_white').style.marginLeft='-'+parseInt(width_end/2+20)+'px';stop_animation();opacity(img_end);}},20);}
else{opacity(img_end);}}}
function opacity(img_end)
{stop_animation();var op=0;timer_opacity=setInterval(function(){op=op+0.1;img_end.style.opacity=op;if(op>1)
{img_end.style.opacity=1;stop_animation();}},20);}
function close()
{stop_play();stop_animation();if(typeof(dan_lightbox_white)!=="undefined"){document.body.removeChild(dan_lightbox_white);}
if(typeof(dan_lightbox_waiting)!=="undefined"&&document.getElementById('dan_lightbox_waiting')!=null){document.body.removeChild(dan_lightbox_waiting);}
document.body.removeChild(dan_lightbox_black);}
function next()
{stop_play();stop_animation();m=num();var n=m+1;if(m>dan_show_length-1){m=0;}
if(n>dan_show_length-1){n=0;}
var img_start=dan_lightbox[m];var img_end=dan_lightbox[n];resize(img_start,img_end);}
function prev()
{stop_play();stop_animation();m=num();var n=m-1;if(m<0){m=dan_show_length-1;}
if(n<0){n=dan_show_length-1;}
var img_start=dan_lightbox[m];var img_end=dan_lightbox[n];resize(img_start,img_end);}
function play()
{if(play_start==0)
{play_start=1;document.getElementById('dan_lightbox_nav_play').style.backgroundPosition='0px -40px';m=num();var n=m+1;if(n>dan_show_length-1){n=0;}
var img_start=dan_lightbox[m];var img_end=dan_lightbox[n];resize(img_start,img_end);m++;if(m>dan_show_length-1){m=0;}
timer_play=setInterval(function(){n=m+1;if(n>dan_show_length-1){n=0;}
if(!img_end.complete){stop_play();stop_animation();waiting(1);img_end.onload=function(){waiting(0);play();return false;}}
img_start=dan_lightbox[m];img_end=dan_lightbox[n];resize(img_start,img_end);m++;if(m>dan_show_length-1){m=0;}},3000);}
else
{play_start=0;document.getElementById('dan_lightbox_nav_play').style.backgroundPosition='0px 0px';stop_play();stop_animation();}}
function num()
{for(var k=0;k<dan_show_length;k++)
{if(dan_lightbox[k].style.display=='inline')
{return k;}}}
function stop_animation()
{if(typeof(timer_resize_height)!=="undefined"){clearTimeout(timer_resize_height);}
if(typeof(timer_resize_width)!=="undefined"){clearTimeout(timer_resize_width);}
if(typeof(timer_opacity)!=="undefined"){clearTimeout(timer_opacity);}}
function stop_play()
{if(typeof(timer_play)!=="undefined")
{play_start=0;document.getElementById('dan_lightbox_nav_play').style.backgroundPosition='0px 0px';clearTimeout(timer_play)}}}