document.addEventListener("DOMContentLoaded", yandex_cashbox_phone);

function yandex_cashbox_phone() {
	var input = document.getElementById("tel");
	VMasker(input).maskPattern("+9 (999) 999-99-99");
}