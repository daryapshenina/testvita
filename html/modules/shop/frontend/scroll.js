function f_mod_shop_scroll(a){var d=document.getElementById("mod_shop_container_"+a);a=document.getElementById("mod_shop_frame_"+a);var b=document.getElementsByClassName("section_item")[0];if(b){var c=b.offsetWidth+2*parseInt(window.getComputedStyle(b,null).marginLeft);a.style.width=c*Math.floor(d.offsetWidth/b.offsetWidth)+"px"}}
function f_mod_shop_scroll_next(a){var d=document.getElementById("mod_shop_wrap_"+a),b=document.getElementById("mod_shop_frame_"+a);parseInt(window.getComputedStyle(d,null).marginLeft);var c=document.getElementsByClassName("section_item")[0],c=c.offsetWidth+2*parseInt(window.getComputedStyle(c,null).marginLeft),e=parseInt(window.getComputedStyle(d,null).marginLeft),f=document.getElementById("mod_shop_scroll_next_"+a);a=document.getElementById("mod_shop_scroll_prev_"+a);var g=-c*Math.ceil(-(e/c)),
b=d.offsetWidth+e-c-parseInt(b.style.width);0<=b&&(d.style.marginLeft=g-c+"px");0>=b&&(f.style.display="none");a.style.display="block"}
function f_mod_shop_scroll_prev(a){var d=document.getElementById("mod_shop_wrap_"+a);document.getElementById("mod_shop_frame_"+a);parseInt(window.getComputedStyle(d,null).marginLeft);var b=document.getElementsByClassName("section_item")[0],b=b.offsetWidth+2*parseInt(window.getComputedStyle(b,null).marginLeft),c=parseInt(window.getComputedStyle(d,null).marginLeft),e=document.getElementById("mod_shop_scroll_next_"+a);a=document.getElementById("mod_shop_scroll_prev_"+a);var f=b*Math.ceil(c/b);0>c&&(d.style.marginLeft=
f+b+"px");0<=f+b&&(a.style.display="none");e.style.display="block"};