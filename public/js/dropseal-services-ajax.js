(function( $ ) {
	"use strict";
	$(function() {

		//highlighting empty fields when submitting a form
	    function lightEmpty(form) {
	      	form.find('.empty-field').css({'border-color':'#d8512d'});
	      	let check = form.find('input[type="checkbox"].empty-field').length;
	      	if( check != 0 ) {
	      		form.find('.requir').css({'color':'#d8512d'});
	      	}
	      	if( form.find('.empty-field').length || form.find('.sms-invalid').length ) {
	      	    $([document.documentElement, document.body]).animate({
			        scrollTop: $('.empty-field, .sms-invalid').offset().top-40
			    }, 1500);	      		
	      	}
	      	setTimeout(function() {
		    	form.find('.empty-field').removeAttr('style');
		    	form.find('.requir').removeAttr('style');
	      	}, 4500);
	    }

	    function createFormData (action, formClass, totalPaid, orderID, status) {
    		let formData = new FormData();
    		let fileData = $('.dss-upload-file input').prop('files')[0];
    		formData.append('file', fileData);
	        let otherData = $('form.' + formClass).serializeArray();
			$.each(otherData, function(key, input) {
     			formData.append(input.name, input.value);
			});
			if(totalPaid) {
				formData.append('total_paid', totalPaid);
				formData.append('payment_status', status);
			}
			if( orderID ) {
        		formData.append('id', orderID);						
			}
			formData.append('action', action);
			return formData;    	
	    }

		const ajaxurl = window.location.origin+'/wp-admin/admin-ajax.php';
	    function serviceFormAjax(data, totalPaid) {
	    	$('.dss-notification-container').html('');
			$.ajax({
				url         : ajaxurl,
				type        : 'POST',
				data        : data,
				cache       : false,
				dataType    : 'json',
				// disable processing of transmitted data
				processData : false,
				// disable setting request type header
				contentType : false,
				success     : function( response ){
					if( response.order_id ){
						let paySystem = data.get('pay');
						if( paySystem == 'pay_pal' ) {
							let returnUrl = $('input[name="return"]').val();			
					        let pos  = returnUrl.indexOf('?id=');
					        let url = returnUrl.slice(0, pos);
					        url = url + '?id=' + response.order_id;
							$('.sms-paypal-wrap input[name="return"]').val(url);
							$('.sms-paypal-wrap input[name="item_number"]').val(response.order_id);
							$('.sms-paypal-wrap input[name="amount"]').val(totalPaid);
							$('.dss-payment-buttons').show();
							$('.sms-next-wrap').hide();
							$('.sms-paypal-wrap').show();
							return;							
						}else{
							let returnUrl = $('input[name="success_url"]').val();
							let pos  = returnUrl.indexOf('?id=');
							let url = returnUrl.slice(0, pos);
							url = url + '?id=' + response.order_id;
							$('.sms-applepay-wrap input[name="success_url"]').val(url);
							$('.sms-applepay-wrap input[name="item_name"]').val(response.order_id);
							$('.sms-applepay-wrap input[name="item_price"]').val(totalPaid);
							totalPaid = Number(totalPaid) * 100;
							$('.sms-applepay-wrap input[name="item_amount"]').val(totalPaid);
							$('.stripe-button').attr('data-amount', totalPaid);
							$('.dss-payment-buttons').show();
							$('.sms-next-wrap').hide();
							$('.sms-applepay-wrap').show();
							return;	
						}
					}
					if( response.url ){
						window.location.replace(response.url);
					}
					else{
						$('.dss-notification-container').append(response);
						$('html,body').animate({scrollTop: 0}, 1000);			
					}
				},
				error: function( jqXHR, status, errorThrown ){
					let html = `
								<div class="message-error">
									Something went wrong. Please try again later.
								</div>
						`;
					$('.dss-notification-container').append(html);
					$('html,body').animate({scrollTop: 0}, 1000);
				}
			});		    	
	    }

		function checkSubmitForm(button, paid) {
			let form = $('.rfield');
			if($(button).hasClass('disabled') ) {
				lightEmpty(form);
	        	return;
			}
			if( paid ) {
				if( $('input[name=pay]:checked').val() == undefined ) {
					$('.dss-notification-container').html('');
					let html = `
								<div class="message-error">
									Please select a payment system.
								</div>
						`;
					$('.dss-notification-container').append(html);
					$('html,body').animate({scrollTop: 0}, 1000);
					return;
				}
			}
			return true;
		}

		// sms service
		$('#sms-next').on('click', function(e) {
			e.preventDefault();
			let totalPaid = Number( $('#dss-total-pay').text() );
			if( !checkSubmitForm( $(this), totalPaid ) ) return;
			let data;
			if(totalPaid) {
				data = createFormData ('save_sms_service_data', 'sms-form', totalPaid, null, 'on_hold');
				// Array.from(data).forEach(e => console.log(e));
				serviceFormAjax(data, totalPaid);
				return;
			}
			data = createFormData ('save_sms_service_data', 'sms-form');
			// Array.from(data).forEach(e => console.log(e));
			serviceFormAjax(data);
		});

		// editing fields before payment
		$('input[type="text"], input[type="phone"], input[type="url"], input[type="checkbox"], input[type="radio"], select').on('change', function() {
			$('.sms-next-wrap').show();
			$('.sms-paypal-wrap').hide();
			$('.sms-applepay-wrap').hide();
		})	

		// custom service request
		$('#crs-submit').on('click', function(e) {
			e.preventDefault();
			if( !checkSubmitForm( $(this) ) ) return;
			let data = createFormData ('save_crs_service_data', 'crs-form');
			// Array.from(data).forEach(e => console.log(e));
			serviceFormAjax(data);
		});

		const smsID = $('.dss-sms-container').data('sms-id');
		$('#sms-update').on('click', function(e) {
			e.preventDefault();
			if( !checkSubmitForm( $(this) ) ) return;
			let data = createFormData ('dss_update_sms_data', 'sms-form', null, smsID);
			// Array.from(data).forEach(e => console.log(e));
			serviceFormAjax(data);
		});	

		// order cancel
		const orderCancel = (id, service, orderID) => {
			$(id).on('click', function(e) {
				e.preventDefault();
				if( confirm('Confirm cancellation!') ) {
					$.post(ajaxurl, {action: 'dss_public_handlers', request: 'dss_change_order_status', service: service, id: orderID, status: 'cancelled',}, function(response) {
						response = JSON.parse(response);
						alert( response + ' canceled');
				        let fullUrl = window.location.href;
				        let hostname = window.location.hostname;
				        let pos  = fullUrl.indexOf(hostname);
				        pos  = fullUrl.indexOf(hostname) + hostname.length;
				        let url = fullUrl.slice(0, pos) + '/';
				        let uri;				        
						if( response == 'Order' ) {
							uri = 'my-account/crs-orders';
						}
						if( response == 'Message' ) {
							uri = 'my-account';
						}
						window.location.assign(url + uri);					
					});
				}
			});			
		}

		const crsID = $('.dss-crs-container').data('crs-id');
		orderCancel('#crs-cancel', 'crs', crsID);
		orderCancel('#sms-cancel', 'sms', smsID);

	});

})( jQuery );