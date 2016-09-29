jQuery(document).ready(function() {
	var current_url = window.location.href;
	var order_id = current_url.toString().split('order_id=');
	order_id = order_id[1];
	var url = 'http://www.vanitycounters.com/order-details?order_id='+order_id;

	jQuery(".customer-details-outer-new").load(url+' .customer-details-outer', function(response, status, xhr) {
		if (status == "error") {
		var msg = "Sorry but there was an error: ";
		jQuery("#error").html(msg + xhr.status + " " + xhr.statusText);
		}
	});
	
	jQuery(".order-details").load(url+' .order-custom-details-outer', function(response, status, xhr) {
		if (status == "error") {
		var msg = "Sorry but there was an error: ";
		jQuery("#error").html(msg + xhr.status + " " + xhr.statusText);
		}
	});

	jQuery('.active').show();
	
});
