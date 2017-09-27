(function( $ ) {
	'use strict';
	class RDTP {

		constructor() {
			this.bindSubmit();
			this.checkboxButtonClickBinding();
			this.stepChangedBinding();
			this.backupWPConfigBinding();
		}

		bindSubmit() {
			const $form = $( '#rdtp-form' );
			$form.on( 'submit', ( e ) => {
				e.preventDefault();
				$form.find('button[type=submit]').attr( 'disabled', true ).text( 'Working...' );
				this.handleSubmit( $form );
			} );
		}

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
				action: 'rdtp_rename_db_table_prefix',
			} );
			let req = $.post( window.ajaxurl, data );

			req.done( ( data, status, xhr ) => {
				let step4 = $( '#rdtp-step4' );
				$form.find('button[type=submit]').text('Done');
				step4.addClass( data.success ? 'success' : 'error' );
				if( data.success ) {
					$( '.rdtp-current-prefix' ).first().text( $('#rdtp-step3 input[name=new-prefix]').val() );
				} else {
					step4.find( '.error' ).append( '<span class="pre">' + data.data + '</span>' )
				}
				this.nextStep();
			} );
		}

		getCurrentPrefix() {
			return $( '.rdtp-current-prefix' ).first().text();
		}

		showError( error ) {
			// Todo: add visual error display
			console.log( error );
		}

		nextStep() {
			$( '.rdtp-step.active' ).removeClass( 'active done' ).addClass( 'done' )
				.next().addClass( 'active' );
		}

		prevStep() {
			$( '.rdtp-step.active' ).removeClass( 'active done' )
				.prev().addClass( 'active' ).removeClass( 'done' );
		}

		checkboxButtonClickBinding() {
			$( 'label.radio' ).on( 'click', ( e ) => {
				let $label = $( e.currentTarget );
				let $sibling = $label.siblings( 'label.radio' );
				let checked = null;

				$label.add( $sibling ).each( ( i, e ) => {
					let $e = $( e );
					if ( $e.find( 'input' ).prop( 'checked' ) ) {
						$e.addClass( 'checked' );
						checked = $e;
					} else {
						$e.removeClass( 'checked' );
					}
				} );
			} );
		}

		stepChangedBinding() {
			$( 'label.radio input' ).on( 'change', ( e ) => {
				let $radio = $( e.currentTarget );
				let $step = $radio.closest( '.rdtp-step' );

				this.nextStep();
			} )
		}

		backupWPConfigBinding() {
			$( '#rdtp-perform-config-backup' ).on( 'change', ( e ) => {
				let $input = $( e.currentTarget );
				if ( $input.prop( 'checked' ) ) {

					let nonceVal = $( '#rdtp-backup-wp-config' ).val();

					let req = $.post( window.ajaxurl, {
						action: 'rdtp_backup_wp_config',
						'rdtp-backup-wp-config': nonceVal
					} );

					req.always( ( data, status, xhr ) => {
						let response = {};
						let $responseText = $( '<span/>' ).addClass( 'backup-response' ).removeClass( 'success error' );
						$input.closest( 'p' ).remove( '.backup-response' ).append( $responseText );

						try {
							response = JSON.parse( data );
						} catch ( e ) {
							console.log( xhr );
							response = { error: xhr.responseText };
						}

						if ( 'success' === status ) {
							if ( response.hasOwnProperty( 'success' ) ) {
								$responseText.addClass( 'success' ).text( response.success );
								return;
							}
						}

						// Error state
						$input.parent().addClass( 'error' );
						let text = 'An error occurred while attempting to create wp-config.php backup.';
						if ( response.hasOwnProperty( 'error' ) ) {
							text += ' "' + response.error + '"';
						}
						$responseText.addClass( 'error' ).text( text );
						this.prevStep();

					} );
				}
			} );
		}

	}

	$( 'document' ).ready( function() {
		new RDTP();
	} ); //docready

})( jQuery );
