(function( $ ) {
	'use strict';

    $(function() {
		/**
		 * datepicker settings
		 */
	 	// add custom localization
	 	$.fn.datepicker.language['en'] = {
		    days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		    daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		    daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
		    months: ['January','February','March','April','May','June', 'July','August','September','October','November','December'],
		    monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		    today: 'Today',
		    clear: 'Clear',
		};
	 	// actually datepicker
		let time = $('#dss-ampm').data('dss-date');
		if( time == undefined ) return;
		time = Number( time + '000');
	 	let start = new Date(time);
		let options = {
			timeZone: 'UTC',
			hour: 'numeric',
			minute: 'numeric',
		};
		let dateString = start.toLocaleString("en-US", options);
		let startHours = dateString.split(':')[0];
		let startMinutes = dateString.split(':')[1];
		startMinutes = startMinutes.slice(0, 2);
		start.setHours(startHours);
		start.setMinutes(startMinutes);

		$('.datepicker-here').datepicker({
	 		minDate: start,
			startDate: start,
			dateFormat: "MM d, yyyy,",
	 		timeFormat: "hh:ii AA",
	 		language: 'en',
	 		onSelect: function(formattedDate,date,inst) {
	 			$('#dss-input-date').removeAttr('style');
	 			$('#dss-input-date').removeClass('empty-field');
	 		},
	 		autoClose: true
 		});

		/**
		 * shows a line with the current date and time in the form of SMS service
		 * outputs date("h:i:s A")
		 */ 
		function changeTime(time) {
			let options = {
				year: 'numeric',
				month: 'long',
				day: 'numeric',
				weekday: 'long',
				timeZone: 'UTC',
				hour: 'numeric',
				minute: 'numeric',
			};

			let interval = setInterval( function() {
				time = time + 1000;
				$('#dss-ampm').attr('data-dss-date', time);
				let date = new Date(time);
				let dateString = date.toLocaleString("en-US", options);
				$('#dss-ampm').html(dateString);
			}, 1000 );
		}

		changeTime(time);

    });

})( jQuery );