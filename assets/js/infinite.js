$(document).ready(function() {
	var win = $(window);
	var page = 1;
	
	win.scroll(function() {
		console.log($(document).height() + " " + win.height() + " " + Math.round(win.scrollTop()));
		
		if ($(document).height() - win.height() == Math.round(win.scrollTop())) {
			//alert($(document).height() + " " + win.height() + " " + win.scrollTop());
			
			$('#loading').show();
			$.ajax({
				url: "/post-scroll?page=" + page,
				method: "GET",
				dataType: 'html'
			})
			.done(function(data) {
				$('#posts').append(data);
				$('#loading').hide();
				page++;
			})
			.fail(function(e) {
				console.log("fail", e);
			});
		}
	});
});