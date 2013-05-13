var load = '#load';
var node = '#node';
var container = '#content';

function ajax_load(path, load) {

	$(load).stop().animate({opacity: 0}, 250);
	$('body, nav a').css('cursor', 'wait');
	$('ul.nav.ajax-load a').parent('li').removeClass('active').parent('ul').parent('li').removeClass('active open');
	$('ul.nav.ajax-load a').filter('[href="' + path + '"]').parent('li').addClass('active').parent('ul').parent('li').addClass('active');

	$(load).load(path + ' ' + node, function(response, status, xhr) {
		if (status == "error") {
			document.title = xhr.statusText;
			$(container).empty().load('/error/' + xhr.status + '/ #node');
		}
		else {
			$(container).show();
			if ($(container).find('form').length) {
				$.getScript("/js/form.js");
			}
			if ($(node + '[data-callback]', response).length) {
				eval('(' + $(node).attr('data-callback') + ')');
			}
			document.title = $(node).attr('data-sitetitle');
		}
		$(load).stop().animate({opacity: 1}, 250);
		$('body, nav a').css('cursor', '');
	});
}

function ajax_event() {
	$('ul.nav.ajax-load a:not(.dropdown-toggle)').on('click', function () {
		var path = $(this).attr('href');
		ajax_load(path, load);
		window.history.pushState('', '', path);
		return false;
	});
}

$(function () {
	ajax_event();
	window.onpopstate = function(event) {
		ajax_load(window.location.pathname, load);
	}
});
