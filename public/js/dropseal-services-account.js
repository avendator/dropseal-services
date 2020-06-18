(function( $ ) {
	"use strict";
	$(function() {
		
		const ajaxurl = window.location.origin+'/wp-admin/admin-ajax.php';
		// order delete
		$('.order-delete').on('click', function() {
			if( confirm('Confirm deletion!') ) {
				let service = $(this).data('service');
				let orderID = $(this).data('id');
				$.post(ajaxurl, {
					action: 'dss_public_handlers', 
					request: 'dss_change_order_status', 
					service: service,
					id: orderID,
					status: 'trash',
				}, function(response) {
					alert( JSON.parse(response) + ' deleted');	
					location.reload();				
				});
			}
		});

		$('.dss-account-menu-select-mob').on('click', function() {
			$('.dss-user-notice div').text('');
			$('.dss-register-notice').text('');
			$('.dashboard-menu-container ul').toggle();
		});

	});

})( jQuery );