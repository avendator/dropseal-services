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

		//Clear file name
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

        // checkout data / Service cost
        const smsPackageCost =  Number( $('#sms-limit').data('sms-package-cost') );
	 	const mobileCarrierCost = Number( $('#mobile-carrier-cost').val() );
	 	const smsPackageQty = Number( $('#sms-limit').data('sms-package-qty') );
 		const smsLimit = Number( $('#sms-limit').data('sms-limit') );
	 	if( !smsLimit ) {
	 		$('.dss-package-sms').css('display', 'flex');
			let totalPay = Number( $('#dss-total-pay').html() );
			totalPay = smsPackageCost + totalPay;
			$('#dss-total-pay').html(totalPay);	 		
	 	}
	 	const commission = Number( $('#dss-percentage').val() );
		/**
	 	 * add recipient
	 	 */
		let counterRecipients = 1;
		let qtyRecipients = $(".dss-sms-container").data('qty-recipients');
 		// if the message is edited in your account
		let editableQtyRecipients = $(".dss-sms-container").data('editable-qty-recipients');

		//change counter, quantity if edited
		if(!editableQtyRecipients) {
			counterRecipients++;
		} else {
			qtyRecipients -= editableQtyRecipients;
			counterRecipients += editableQtyRecipients;
		}
		/**
		 * displays a warning if the limit is over, adds the amount for the message packet to the checkout.
  		 * Reduces SMS limit by 1 if available
		 */
		const checkSmsLimit = (smsPackageQty, smsPackageCost) => {
			$('.dss-notification-container').html('');
			let smsLimit = Number( $('#sms-limit').val() );
			smsLimit--;
			if( smsLimit >= 0 ) {
				$('#sms-limit').val(smsLimit);
				return;
			}
			let totalPay = Number( $('#dss-total-pay').html() );
			totalPay = smsPackageCost + totalPay;
			$('#dss-total-pay').html(totalPay);
			smsLimit = smsPackageQty - 1;
			$('#sms-limit').val(smsLimit);
			$('.dss-package-sms').css('display', 'flex');
			$('#dss-sms-package').html(smsPackageCost);
			let html = `
				<div class="message-error">The limit of available messages has been reached. The next message will be paid!</div>
			`;
			$('.dss-notification-container').append(html);
			$('html,body').animate({scrollTop: 0}, 1000);
			return;			
		}

	 	const checkMobileCarrier = () => {
	 		let mobileCarrier = Number( $('#dss-mobile-carrier').html() );
	 		if(mobileCarrier) {
	 			$('.dss-mobile-carrier').css('display', 'flex');
	 		}else{
	 			$('.dss-mobile-carrier').hide();
	 		}
	 	}

	 	checkMobileCarrier();

		const ajaxurl = window.location.origin+'/wp-admin/admin-ajax.php';
		//add recipient
		$("#add-recipient").on('click', function(e) {
			e.preventDefault();
			let html;
			if( qtyRecipients == '0' ) return;
			checkSmsLimit(smsPackageQty, smsPackageCost);			

			$.post(ajaxurl, {action: 'dss_public_handlers', request: 'dss_get_mobile_carriers',}, function(response) {
				let mobileCarriers = JSON.parse(response);
				let mobCarriers = JSON.parse(mobileCarriers[0]);
				let tooltip = mobileCarriers[1];
				html = `
					<div class="dss-recipient-wrapper" data-recipient="${counterRecipients}">
						<div class="dss-recipient-row">
							<div class="dss-recipient-name"
								<label class="required dss-field-title">Name:</label>
								<span class="input-error"></span>			
								<input type="text" name="name_${counterRecipients}" class="dss-form-field empty-field">
							</div>
							<div class="dss-recipient-email" data-recipient-email="${counterRecipients}">
								<input type="checkbox" name="email_delivery" id="email-delivery-${counterRecipients}">
								<label for="email-delivery-${counterRecipients}" class="dss-field-title">Add e-mail delivery</label>
							</div>
						</div>
						<div class="dss-recipient-row">
							<div class="dss-recipient-phone"
								<label class="required dss-field-title">Phone Number:</label>
								<span class="input-error"></span>
								<input type="tel" name="phone_${counterRecipients}" placeholder="e.g. 1617555121" class="dss-form-field empty-field">
							</div>
							<div>
								<label class="dss-field-title">Select Mobile Carrier</label>
								<a href="#" data-tooltip="${tooltip}" class="dss-tooltipe-link">
									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="mdi-alpha-i-circle-outline" width="24" height="24" viewBox="0 0 24 24" style="fill: #191970"><path d="M14,7V9H13V15H14V17H10V15H11V9H10V7H14M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4Z" />
									</svg>
								</a>
			    				<select name="mobile_carrier_${counterRecipients}" class="sms-mobile-carriers">
		    						<option value="" selected></option>`;

									$.each(mobCarriers, function( k, v ) {
										if( v.checked == 'checked' ) {
											html += `<option value="${v.sms}">${v.name}</option>`;										
										}
									});

								html += `</select>
							</div>
						</div
						<div>
							<div class="remove-recipient-wrap">
								<button class="remove-recipient">Remove Recipient</button>
							</div>
						</div>
					</div>`;

				$(".sms-recipient-container").append(html);
				let mobileCarrier = Number( $('#dss-mobile-carrier').html() );
				mobileCarrier += mobileCarrierCost;
				$('#dss-mobile-carrier').html(mobileCarrier);
				let totalPay = Number( $('#dss-total-pay').html() );
				totalPay = mobileCarrierCost + totalPay;
				$('#dss-total-pay').html(totalPay);
				counterRecipients++;
				qtyRecipients--;
				checkMobileCarrier();
				checkoutServiceCheck(totalPay);
			});			
		});
 		/**
	 	 * remove recipient block
	 	 * add a fee to the check out for the absence of a mobile operator, 
	 	 * reducing the limit for sending messages
		 * Checkout calculation
	 	 */
		$(".sms-recipient-container").on('click', ".remove-recipient", function(e) {
			e.preventDefault();
			counterRecipients--;
			qtyRecipients++;
			let newMobileCarrier = Number( $('#dss-mobile-carrier').html() );
			let mobileCarrier = mobileCarrierCost;
			// if mobileCarrier selected
			if( $(this).closest('.dss-recipient-wrapper').find('.sms-mobile-carriers option:selected').val() ) {
				mobileCarrier = 0;
			}
			if( newMobileCarrier && mobileCarrier ) {
				newMobileCarrier -= mobileCarrierCost;
				$('#dss-mobile-carrier').html(newMobileCarrier);
			}
			$(this).closest('.dss-recipient-wrapper').remove();
			let totalPay = Number( $('#dss-total-pay').html() );
			totalPay -= mobileCarrier;
			$('#dss-total-pay').html(totalPay);
			let newSmsLimit = Number( $('#sms-limit').val() );
			newSmsLimit++;
			if( newSmsLimit == smsPackageQty ) {
				newSmsLimit = 0;
				if( newSmsLimit != smsLimit ) {
					$('.dss-package-sms').hide();
					totalPay = totalPay - smsPackageCost;
					$('#dss-total-pay').html(totalPay);					
				}else{
					newSmsLimit = smsPackageQty;
				}
			}
			$('#sms-limit').val(newSmsLimit);
			checkMobileCarrier();
			checkoutServiceCheck(totalPay);
		});

		// show / hide recipient email input
		$('.sms-recipient-container').on('change', 'input[type="checkbox"]', function() {
			let index = $(this).parent().data('recipient-email');
			let html = `
				<input type="email" name="email_${index}" class="dss-form-field empty-field">
			`;
			if( $(this).prop('checked') ) {
				$(this).parent().append(html);
			}else{
				$(this).parent().find('input[type="email"]').remove();
			}
		});

		const onlyNumberEnter = (container, className) => {
			$(container).on('click', className, function() {
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
		
		onlyNumberEnter('.sms-recipient-container', '.dss-recipient-phone');
		onlyNumberEnter('.dss-send-money-container', '.sms-send-money');

/***********************************************************************************
	# checking required fields for void
 ***********************************************************************************/
		const checkVoidInputs = (container, className, element) => {
			$(container).on('click', className, function(){
				let input = $(this).find(element);
				input.keyup(function(){
					$(this).removeAttr('style');
					if( $(this).val() < 1 ){
						$(this).addClass('empty-field');
					}else{
						$(this).removeClass('empty-field');
					}
					
				});
			});
		}

		checkVoidInputs('.dss-sms-container', '.dss-message-block', '.message-text');
		checkVoidInputs('.dss-crs-container', '.dss-message-block', '.message-text');
		checkVoidInputs('.sms-recipient-container', '.dss-recipient-phone', 'input');
		checkVoidInputs('.sms-recipient-container', '.dss-recipient-name', 'input');
		checkVoidInputs('.sms-recipient-container', '.dss-recipient-email', 'input');
		checkVoidInputs('.dss-send-money-wrap', '.sms-pay-email', 'input');
		checkVoidInputs('.dss-send-money-wrap', '.sms-send-money', 'input');

		// confirmation of terms and conditions
		$('#sms-conditions').on('change', function() {
	        if( $('#sms-conditions').prop('checked') ) {
	        	$('#sms-conditions').removeClass('empty-field');
	        }else{
	        	$('#sms-conditions').addClass('empty-field');
	        }
	    });
/*********************************************************************************
 	#  Checkout hadlers
 *********************************************************************************/
 	 	// check field "Total Pay"
	 	const checkoutServiceCheck = (totalPay) => {
	 		if( Number(totalPay) ) {
	 			$('.dss-checkout').show();
	 			$('.dss-payment-buttons').show();
	 		}else{
	 			$('.dss-checkout').hide();
	 			$('.dss-payment-buttons').hide();
	 		}
	 	}

	 	let totalPay = Number( $('#dss-total-pay').html() );
	 	checkoutServiceCheck(totalPay);

		/**
	 	 * show / hide payment inputs & checkout part
	 	 */
        $('#sms-send-money').on('change', function() {
            if( $(this).prop('checked') ) {
            	$('.dss-send-money-wrap div').show();
            	$('.dss-send-money-wrap input').addClass('empty-field');
            }else{
            	$('.dss-send-money-wrap div').hide();
                let money = Number( $('#sms-money-amount').val() );
                let percentege = Number( $('#dss-commission').html() );
                let totalPay = Number( $('#dss-total-pay').html() );
                totalPay = Math.round( totalPay - money - percentege );
                $('#dss-total-pay').html(totalPay);
              	$('#dss-commission').html('');
                $('#dss-transfer').html('');
                $('.add-service-wrapper').hide();
            	$('.dss-send-money-wrap input').val('').removeClass('empty-field sms-invalid');
            }
            checkoutServiceCheck(totalPay);
        });

        // payment input validation
        $('.sms-pay-email').blur(function() {
        	$(this).removeAttr('style');
        	let pattern = /^([a-z0-9_\.-])+[@][a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
			if( pattern.test($(this).val()) ) {
				$(this).removeClass('sms-invalid');
			}else{
    			$(this).addClass('sms-invalid');
        	}
		});
	 	/**
	 	 * get the amount of money transfer & its commission
	 	 */
	 	$('#sms-money-amount').keyup(function() {
	 		$(this).removeAttr('style');
	 		let mobileCarrier = Number( $('#dss-mobile-carrier').html() );
	 		let money = Number( $(this).val() );
	 		if(money) {
	 			$('.add-service-wrapper').css('display', 'flex');
	 		}else{
	 			$('.add-service-wrapper').hide();
	 		}
        	$('#dss-transfer').text(money);
	 		let percentage = ( money * commission ) / 100;
	 		$('#dss-commission').html(percentage);
	 		let totalPay = percentage + money + mobileCarrier;
	 		totalPay = totalPay.toFixed(2);
	 		$('#dss-total-pay').html(totalPay);
	 		checkoutServiceCheck(totalPay);
		});
	 	/**
	 	 * get the value of the service price (mobile carrier)
	 	 * checking fields for void
	 	 */

	 	$('.sms-recipient-container').on('change', '.sms-mobile-carriers', function() {
	 		let totalPay = Number( $('#dss-total-pay').html() );
 			let mobileCarrier = Number( $('#dss-mobile-carrier').html() );
 			if( $(this).val() ) {
 				if(mobileCarrier) {
 					totalPay -= mobileCarrierCost;
 					totalPay = totalPay.toFixed(2);
					mobileCarrier -= mobileCarrierCost;
					$('#dss-mobile-carrier').html(mobileCarrier);
 					$('#dss-total-pay').html(totalPay);					
 				}else{
 					$('.dss-mobile-carrier').hide();
 				}
 			}else{
 				totalPay += mobileCarrierCost;
 				$('#dss-total-pay').html(totalPay);
				mobileCarrier += mobileCarrierCost;
				$('#dss-mobile-carrier').html(mobileCarrier);
 			}
 			checkMobileCarrier();			
 			checkoutServiceCheck(totalPay);
	 	});
/*********************************************************************************
 # END Checkout hadlers
 *********************************************************************************/
	 	// validation of data entry in the field
	 	const checkInvalidField = (container, className, lengthMin, lengthMax, message) => {
	 		$(container).on('click', className, function(){
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
	 		});
	 	}

 		checkInvalidField('.sms-recipient-container', '.dss-recipient-phone', 10, 10, '10 digits');
 		checkInvalidField('.sms-recipient-container', '.dss-recipient-name', 3, 15, 'min 3 letters - max 15');	 		

		const emailValidation = (container, className, element) => {
			$(container).on('click', className, function(){
				let email = $(this).find(element);
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
		}

		$('.sms-recipient-container').on('click', '.dss-recipient-wrapper', function(){
			let id = $(this).data('recipient');
			let input = 'input[name="email_' + id + '"]';
			emailValidation('.sms-recipient-container', '.dss-recipient-email', input);
		});

		emailValidation('.dss-send-money-wrap', '.sms-pay-email', 'input');

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

    });

})( jQuery );