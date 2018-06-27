function timer()
{
	timer_ends = document.getElementById('timer_value').getAttribute('data-end');
	var period = timer_ends * 1e3 - Date.now();
	if (period < 0){period = 0;}
	
	period_s = Math.floor(period / 1e3);
	var days = Math.floor(period_s / 86400);
	period_s = period_s % 86400;
	var hours = Math.floor(period_s / 3600);
	period_s = period_s % 3600;
	var mins = Math.floor(period_s / 60);
	period_s = period_s % 60;
	var secs = Math.floor(period_s);

	document.getElementById('timer_value').innerHTML = '<div class="timer_flip"><div class="timer_blackout"></div>' + days + '</div><div class="timer_flip"><div class="timer_blackout"></div>' + hours + '</div><div class="timer_flip"><div class="timer_blackout"></div>' + mins + '</div><div class="timer_flip"><div class="timer_blackout"></div>' + secs + '</div>';

	if (period > 0){setTimeout(timer, 1000);}
}