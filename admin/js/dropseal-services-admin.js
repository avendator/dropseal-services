(function( $ ) {
	'use strict';
	$(function() {

		/*
		* Orders
		*/
		//orders filter
		$(".dss_orders_filter").on('keyup', function() {
			let value = $(this).val().toLowerCase();
			$(".dss_orders_container tbody tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
			});
		});

		//select all checked
		$(".dss_select_all_checked").on('click', function() {
			if ($(this).is(':checked')) {
				$(".dss_orders_container tbody tr").each(function() { 
					if ($(this).is(':visible')) {
						$(this).find("input:checkbox").prop('checked', true);
					} 
				});
			} else {
				$(".dss_orders_container tbody input:checkbox").prop('checked', false);
			}
		});

		/*
		* Order
		*/
		let counterRecipients = 1;
		let qtyRecipients = $(".dss_order_container").data('qty-recipients');

		//if edited
		let editableQtyRecipients = $(".dss_order_container").data('editable-qty-recipients');

		//change counter, quantity if edited
		if(!editableQtyRecipients) {
			counterRecipients++;
		} else {
			qtyRecipients -= editableQtyRecipients;
			counterRecipients += editableQtyRecipients;
		}

		// select file
		$(".dss_upload_file input").on('change', function() {
			let maxFileSize = $(".dss_upload_file").data('file-size') * 1024 * 1024;
			let file = this.files[0];

			if (file.size > maxFileSize) {
				$('.dss_upload_file_name').text(`Max file size ${maxFileSize} MB`).css('color', 'red');
			} else {
				let html = `
					<span>${file.name}</span>
					<span class="dss_delete_file_btn dss_clear_file" title="Delete file">&#10007;</span>
				`;
				$('.dss_upload_file_name').html(html);
			}		
		});

		//delete file
		$(".dss_delete_file").on('click', function() {
			if (confirm('Confirm deletion!')) {
				let service;
				let orderID;
				if ($(this).data('sms-id')){
					service = 'sms';
					orderID = $(this).data('sms-id');
				} else if ($(this).data('crs-id')){
					service = 'crs';
					orderID = $(this).data('crs-id');
				}
				$.post(ajaxurl, {action: 'dss_handlers', request: 'delete_file', service: service, id: orderID,}, function(response) {
					$(".dss_upload_file_name").text('');
				});
			}
		});

		//clear file
		$(".dss_upload_file_name").on('click', ".dss_clear_file", function() {
			$(".dss_upload_file input").val('');
			$(".dss_upload_file_name").text('');
		});

		//add recipient
		$(".dss_add_recipient").on('click', function() {
			if (qtyRecipients) {
				$.post(ajaxurl, {action: 'dss_handlers', request: 'get_mobile_carriers'}, function(response) {			
					let mobileCarriers= JSON.parse(response);
					let html = `
						<div class="dss_recipient_wrapper">
							<div class="dss_recipient_header">            
								<div>
									<span>Recipient</span>
									<span class="dss_recipient_btn dss_delete_recipient" title="Delete recipient">-</span>
								</div>                
							</div>    
							<div class="dss_order_wrapper">
								<div>
									<input type="text" name="recipient_name_${counterRecipients}" placeholder="Name" required>
								</div>
								<div>
									<input type="email" name="recipient_email_${counterRecipients}" placeholder="Email">
								</div>
							</div>
							<div class="dss_order_wrapper">
								<div>
									<input type="text" name="recipient_phone_${counterRecipients}" placeholder="Phone" required>
								</div>
								<div>
									<select name="recipient_mobile_carrier_${counterRecipients}">
										<option value="">Mobile carrier no choice</option>`;
										
										$.each(mobileCarriers, function( k, v ) {
											html += `<option value="${v.sms}">${v.name}</option>`;
										}); 
							
									html += `</select>
								</div>
							</div>
						</div>`;

					$(".dss_order_container").append(html);
					counterRecipients++;
					qtyRecipients--;	
				});
			}	
		});

		//remove recipient
		$(".dss_order_container").on('click', ".dss_delete_recipient", function() {
			$(this).closest(".dss_recipient_wrapper").remove();
			counterRecipients--;
			qtyRecipients++;
		});

		// Modal Box
		$(".dss_add_link").on('click', function() {
			$(".dss_links_modal").show();
		});

		$(".dss_links_modal_close").on('click', function() {
			$(".dss_links_modal").hide();			
		});

		$(".dss_links_modal_confirm").on('click', function() {
			$(".dss_links_modal").hide();
		});

		//add link
		$(".dss_add_link_modal").on('click', function() {
			let html = `
				<div class="dss_links_modal_wrapper">
					<input type="url" name="links[]" placeholder=" URL">
					<span class="dss_links_modal_btn dss_delete_link_modal" title="Delete url">-</span>
				</div>
			`;
			$(".dss_links_modal_container").append(html);
		});

		//remove link
		$(".dss_links_modal_container").on('click', ".dss_delete_link_modal", function() {
			$(this).parent().remove();
		});

		//check locks
		$("input[name='dss_save_new_sms_order']" ).on('click', function(e) {
			let numbers = [];
			let emails = [];

			$("input[name*='recipient_phone_']").each(function() { 			
				numbers.push({name: $(this).attr("name"), val: $(this).val()});
			});

			$("input[name*='recipient_email_']").each(function() { 			
				emails.push({name: $(this).attr("name"), val: $(this).val()});
			});

			$.post(ajaxurl, {action: 'dss_handlers', request: 'check_locks', numbers: numbers, emails: emails}, function(response) {
				response = JSON.parse(response);
				console.log(response)
				// $(".notice").remove();
				// if(response.Error){
				// 	e.preventDefault();
				// 	$(".dss_order_container").prepend(response.Error)
				// 	$.each( response.data, function( key, value ) {
				// 		$("input[name='" + value.name + "']").addClass('dss_error_border');
				// 	});
				// } 
			});		
		});

		$(".dss_recipient_wrapper input").focus(function() {		
			if($(this).hasClass('dss_error_border')) $(this).removeClass('dss_error_border');			
		});

		//delete order
		$(".dss_delete_order").on('click', function() {
			if (confirm('Confirm deletion!')) {			
				let service;
				let orderID;
				$(this).closest("tr").fadeOut('slow');
				
				if ($(this).data('sms-id')){
					service = 'sms';
					orderID = $(this).data('sms-id');
				} else if ($(this).data('crs-id')){
					service = 'crs';
					orderID = $(this).data('crs-id');
				}
				$.post(ajaxurl, {action: 'dss_handlers', request: 'delete_order', service: service, id: orderID}, function(response) {
					// console.log(response)
				});
			}
		});

		/*
		* Settings tab
		*/
		//add blocking phone/email/domain
		$(".dss_add_lock").on('click', function() {
			let type = $(this).data('type');
			let val = $( ".blocking_container input[name='" + type + "']" ).val();
			let lockExists = $(this).siblings(".dss_lock_exists");
			$.post(ajaxurl, {action: 'dss_handlers', request: 'add_lock', type: type, val: val}, function(response) {
				
				if(response == 'exists') {
					lockExists.css('visibility', 'visible');
				} else {
					$(".dss_blocked_" + type).append(response);
					$( ".blocking_container input[name='" + type + "']" ).val('');
				}			
			});
		});

		$(".blocking_container input").focus(function(){
			$(this).siblings(".dss_lock_exists").css('visibility', 'hidden');
		});
		
		//delete blocking phone/email/domain
		$(".blocking_container").on('click', ".dss_delete_lock", function() {
			$(this).parent().fadeOut('slow');
			$.post(ajaxurl, {action: 'dss_handlers', request: 'delete_lock', type: $(this).data('type'), val: $(this).data('val')}, function(response) {
				// console.log(response)
			});
		});

	});
})( jQuery );