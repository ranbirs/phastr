$(function () {
	if (window.history.pushState) {
		$.getScript('/js/ajax_load.html5.js');
	}
	else {
		//$.getScript('/js/ajax_load.hashbang.js');
	}
});
