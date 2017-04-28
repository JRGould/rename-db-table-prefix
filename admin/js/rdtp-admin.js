(function( $ ) {
	'use strict';
	function classRDTP() {
		return {
			construct() {
				const $form = $( '#rdtp-form' );

				$form.on( 'submit', ( e ) => {
					e.preventDefault();
					rdtp.handleSubmit( $form );
				} );
			},
			handleSubmit( $form ) {
				let data = $form.serializeArray().reduce( function( obj, item ) {
					obj[ item.name ] = item.value;
					return obj;
				}, {} );

				if ( this.getCurrentPrefix() === data[ 'new-prefix' ] ) {
					this.showError( 'New prefix must be different from current prefix' );
					return;
				}

				Object.assign( data, {
					action: 'rdtp_test',
				} );
				let req = $.post( window.ajaxurl, data );

				req.done( ( data, status, xhr ) => {
					console.log( 'done!', status, data );
				} );
			},
			getCurrentPrefix() {
				return $( '#rdtp-current-prefix' ).text();
			},
			showError( error ) {
				console.log( error );
			},
		}
	}

	const rdtp = classRDTP();

	$( 'document' ).ready( rdtp.construct ); //docready


})( jQuery );
