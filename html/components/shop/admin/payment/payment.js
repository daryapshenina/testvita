function yandex(){var a=document.getElementById("payment_yandex"),d=document.getElementById("yandex_container"),e=document.getElementById("payment_yandex_ym"),b=document.getElementById("yandex_container_ym"),c=document.getElementById("yandex_container_yc");d.style.display=a.checked?"block":"none";e.checked?(b.style.display="block",c.style.display="none"):(b.style.display="none",c.style.display="block")}
function sber(){var a=document.getElementById("payment_sberbank");document.getElementById("sberbank_container").style.display=a.checked?"block":"none"}DAN_ready(function(){yandex();sber()});