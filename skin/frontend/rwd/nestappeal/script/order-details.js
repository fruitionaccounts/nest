jQuery(document).ready(function() {
	var current_url = window.location.href;
	var order_id = current_url.toString().split('order_id=');
	order_id = order_id[1];
	var url = 'http://www.shopshowers.com/order-details?order_id='+order_id;

	jQuery(".customer-details-outer").load(url+' .customer-details-outer', function(response, status, xhr) {
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
	
	if(order_id){
	jQuery('.active').show();	

	jQuery('#shink-main-container').css('overflow','inherit');
	jQuery('#shink-main-container').css('margin','20px 0px');
	
	var top_dist = jQuery('.polished-left-egde').css('margin-top');
	top_dist = parseFloat(top_dist)+20;
	jQuery('.polished-left-egde').css('margin-top',top_dist);
	jQuery('.backsplash-left-egde').css('margin-top',top_dist);
	jQuery('.backsplash-right-egde').css('margin-top',top_dist);
	jQuery('.polished-right-egde').css('margin-top',top_dist);
	

	jQuery('#shink-1-container .shink-1-left-space').css('top','-20px');
	jQuery('#shink-1-container .shink-2-top-space').css('top','-20px');
	jQuery('#shink-1-container .shink-2-top-space').css('height','20px');

	jQuery('#shink-1-container .shink-2-distance').css('bottom','-20px');
	jQuery('#shink-2-container .shink-2-top-space').css('bottom','-20px');
	jQuery('#shink-2-container .shink-2-top-space').css('height','20px');
	
	}
});
