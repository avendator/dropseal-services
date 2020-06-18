(function( $ ) {
	'use strict';

    $(function() {

		// select file service form
		$('.dss-upload-file input').on('change', function() {
			let FileSize = $(".dss-upload-file").data('file-size');
			let maxFileSize = Number(FileSize) * 1024 * 1024;
			let file = this.files[0];
			if (file.size > maxFileSize) {
				$('.dss-upload-file-name').text(`Max file size ${FileSize} MB`).css('color', 'red');
			} else {
				let fileName;
				if( file.name.length > 50 ) {
					fileName = file.name.substr(1, 50) + '...';
				}else{
					fileName = file.name;
				}
				let html = `
					<span>${fileName}</span>
					<span class="dss-delete-file" id="dss-clear-file" title="Delete file">&#10007;</span>
				`;
				$('.dss-upload-file-name').html(html);
			}			
		});

		// delete file service form
		$(".dss-delete-file").on('click', function() {
			if (confirm('Confirm deletion!')) {
				let order;
				let orderID;
				if ($(this).data('sms-id')){
					order = 'sms';
					orderID = $(this).data('sms-id');
				} else if ($(this).data('crs-id')){
					order = 'crs';
					orderID = $(this).data('crs-id');
				}
				$.post(ajaxurl, {action: 'dss_handlers', request: 'delete_file', service: order, id: orderID,}, function(response) {
					$(".dss-upload-file-name").text('');
				});
			}
		});

		// Clear file name
		$('.dss-upload-file-name').on('click', '#dss-clear-file', function() {
			$(".dss-upload-file input").val('');
			$(".dss-upload-file-name").text('');
		});

		// Modal Box (url-container)
		$(".dss-url-wrapper").on('click', function() {
			$("#dss-add-links-modal").show();
		});

		$(".dss-links-modal-close").on('click', function() {
			$("#dss-add-links-modal").hide();
			$('input[name="links[]"]').val('');
			$('input[name="links[]"]').removeClass('sms-invalid');
			$('.link-error').remove();
		});

		$(".dss-confirm-links-btn").on('click', function() {
			$("#dss-add-links-modal").hide();
		});

		// add link
		$(".dss-add-links-btn").on('click', function() {
			let html = `
					<div class="dss-links-wrapper">
						<input type="url" name="links[]" placeholder="http://">
						<span class="dss-delete-links-btn dss-links-btn" title="Add more url">-</span>
					</div>
			`;
			$(".dss-links-container").append(html);
		});

		// remove link
		$('.dss-add-links-modal').on('click', '.dss-delete-links-btn', function() {
			$(this).parent().remove();
			if( !$('.dss-add-links-modal').find('.sms-invalid').length ) {
				$('.link-error').remove();
			}
		});

		// links validation
		$('#dss-add-links-modal').on('click', function() {
			$('input[name="links[]"]').blur(function() {
				let regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
				$('.link-error').remove();
				if( regexp.test($(this).val()) ) {
					$(this).removeClass('sms-invalid');
	    			$('.link-error').remove();
				}else{
	    			$(this).addClass('sms-invalid');
					let html = `<span class="link-error sms-invalid">Invalid url</span>`;				
					$('.dss-attachement-wrap').append(html);
	        	}
			});			
		});

		// email validation
		$('.dss-recipient-email').on('click', function(){
			let email = $(this).find('input');
			email.blur(function(){
				$(this).removeAttr('style');
				let pattern = /^([a-z0-9_\.-])+[@][a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
				if( pattern.test($(this).val()) ) {
					$(this).removeClass('sms-invalid');
				}else{
	    			$(this).addClass('sms-invalid');
	        	}
			});
		});

		const checkVoidInputs = (className, element) => {
			$(className).on('click', function(){
				let input = $(this).find(element);
				input.keyup(function(){
					if( $(this).val() < 1 ){
						$(this).addClass('empty-field');
					}else{
						$(this).removeClass('empty-field');
					}
					
				});
			});
		}

		checkVoidInputs('.dss-recipient-phone', 'input');
		checkVoidInputs('.dss-recipient-name', 'input');
		checkVoidInputs('.dss-message-block', '.message-text');

		// confirmation of terms and conditions
		$('.dss-form-line').on('change', function() {
	        if( $('#sms-conditions').prop('checked') ) {
	        	$('#sms-conditions').removeClass('empty-field');
	        }else{
	        	$('#sms-conditions').addClass('empty-field');
	        }
	    });

		// enter only numbers
		const phoneNumberEnter = (className) => {
			$(className).on('click', function() {
				let input = $(this).find('input');
				input.keydown(function(e){
				    // Allow: backspace, delete, tab, escape, enter and .
				    if( $.inArray(e.keyCode, [46, 8, 9, 27, 91, 110]) !== -1 ||
				         // Allow: Ctrl+A
				        (e.keyCode == 65 && e.ctrlKey === true) ||
				         // Allow: home, end, left, right
				        (e.keyCode >= 35 && e.keyCode <= 39) ) {
				         // let it happen, don't do anything
				        return;
				    }
				    // Ensure that it is a number and stop the keypress
				    if( (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) ) {
				        e.preventDefault();
				    }
				});					
			});
		}

		phoneNumberEnter('.dss-recipient-phone');

	 	const checkInvalidField = (className, lengthMin, lengthMax, message) => {
	 		let input = $(className).find('input');
 			input.blur(function() {
	        	$(this).removeAttr('style');
	        	$(this).closest(className).find('.input-error').text('');
				if( $(this).val().length < lengthMin ||  $(this).val().length > lengthMax ) {
					$(this).addClass('sms-invalid');
					$(this).closest(className).find('.input-error').text(message);
				}else{
					$(this).removeClass('sms-invalid');
				}
			});
	 	}

	 	$('.dss-recipient-phone').on('click', function(){
	 		checkInvalidField($(this), 10, 10, '10 digits');	 		
	 	});

	 	$('.dss-recipient-name').on('click', function(){
	 		checkInvalidField($(this), 3, 15, 'min 3 letters - max 15');	 		
	 	});

    	$('#sms-reminders').on('change', function() {
    		if( $('#sms-reminders').prop('checked') ) {
	    		let reminders = $(this).data('sms-reminders');
	    		if( reminders == false ) {
	    			$(this).removeAttr('checked');
	    			$('.dss-notification-container').html('');
	    			let html = `
	    				<div class="message-error">To receive notifications on your mobile phone, you need to specify the number and mobile operator in your profile</div>
	    			`;
					$('.dss-notification-container').append(html);
					$('html,body').animate({scrollTop: 0}, 1000);
	    		}
	    	}
    	});

    	$('input[name=pay]').on('change', function() {
			if( $('input[name=pay]:checked').val() == 'pay_pal' ) {
				$('.crs-paypal-wrap').show();
				$('.crs-applepay-wrap').hide();
			}else{
				$('.crs-paypal-wrap').hide();
				$('.crs-applepay-wrap').show();					
			}
    	});


    	const backToOrders = (id, uri) => {
    		$(id).on('click', function(e) {
    			e.preventDefault();
		        let fullUrl = window.location.href;
		        let hostname = window.location.hostname;
		        let pos  = fullUrl.indexOf(hostname);
		        pos  = fullUrl.indexOf(hostname) + hostname.length;
		        let url = fullUrl.slice(0, pos) + '/';
    			window.location.assign(url + uri);	
    		});
    	}

    	backToOrders('#crs-back', 'my-account/crs-orders/');
    	backToOrders('#sms-back', 'my-account/');
    	
		// validation for void the fields "Name" & "Phone" in real time
    	$('.rfield').each(function() {
			let form = $(this);
	        // let btn = form.find('#sms-submit');
	        let btn = form.find('#sms-next');
	        if( btn.length == 0 ) {
	        	btn = form.find('#crs-submit');
	        }
	        if( btn.length == 0 ) {
	        	btn = form.find('#sms-update');
	        }
		    setInterval(function() {
		      	// get number of empty fields
		      	let sizeEmpty = form.find('.empty-field').length;
		      	let sizeInvalid = form.find('.sms-invalid').length;
		      	// a trigger condition on the submit button
		      	if( sizeEmpty == 0 && sizeInvalid == 0 ) {
		        	btn.removeClass('disabled');		      		
		        }else{
			        if( btn.hasClass('disabled') ) {
			          	return false;
			        }else{
			          	btn.addClass('disabled');
			        }	        	
	        	}
        	}, 500);
    	});

    });

})( jQuery );;