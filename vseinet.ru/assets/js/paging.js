$(function() {
	var ctrl = false;
	$(document).focus().keyup(function(event) {
		if (event.which == $.ui.keyCode.CTRL) ctrl = false;
	}).keydown(function(event) {
		if (event.which == $.ui.keyCode.CTRL) ctrl = true;
		if(ctrl)
			if (event.which == $.ui.keyCode.LEFT)
				location.replace($('.paging .prev').prop('href'));
			else if (event.which == $.ui.keyCode.RIGHT)
						location.replace($('.paging .next').prop('href'));
	});
});