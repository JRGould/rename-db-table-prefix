(function( $ ) {
	'use strict';
	$( () => {

		$( '#rdtpForm' ).on( 'submit', ( e ) => {
			e.preventDefault();
			let data = $( '#rdtpForm' ).serializeArray().reduce( function( obj, item ) {
				obj[ item.name ] = item.value;
				return obj;
			}, {} );

			Object.assign( data, {
				action: 'rdtp_test',
			} );
			console.log( data );
			let req = $.post( window.ajaxurl, data );

			req.done( ( data, status, xhr ) => {
				console.log( 'done!', status, data );
			} );
		} );

	} );

})( jQuery );
