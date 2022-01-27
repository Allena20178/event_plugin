(function( $ ) {
	
	
	$( '#ewp-event-start-date' ).datepicker({
		dateFormat: 'MM dd, yy',
		onClose: function( selectedDate ){
			$( '#ewp-event-end-date' ).datepicker( 'option', 'minDate', selectedDate );
		}
	});
	$( '#ewp-event-end-date' ).datepicker({
		dateFormat: 'MM dd, yy',
		onClose: function( selectedDate ){
			$( '#ewp-event-start-date' ).datepicker( 'option', 'maxDate', selectedDate );
		}
	});

})( jQuery );
