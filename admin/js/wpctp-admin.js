(function( $ ) {
	'use strict';
	$( () => {

		$( '#wpctpForm' ).on( 'submit', ( e ) => {
			e.preventDefault();
			let data = $( '#wpctpForm' ).serializeArray().reduce( function( obj, item ) {
				obj[ item.name ] = item.value;
				return obj;
			}, {} );

			Object.assign( data, {
				action: 'wpctp_test',
			} );
			console.log( data );
			let req = $.post( window.ajaxurl, data );

			req.done( ( data, status, xhr ) => {
				console.log( 'done!', status, data );
			} );
		} );

	} );

})( jQuery );
