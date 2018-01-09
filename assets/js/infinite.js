$(document).ready(function() {
	$('#loading').hide();
	var win = $(window);
	var page = 1;
	var working = 0;
	
	// called every time the user scrolls
	win.scroll(function() {
		if ($(document).height() - win.height() == (Math.round(win.scrollTop())) && !working) {
			working = 1;
			
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
				working = 0;
			})
			.fail(function(e) {
				console.log("fail", e);
			});
		}
	});
});